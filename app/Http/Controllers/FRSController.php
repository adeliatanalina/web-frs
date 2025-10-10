<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Matkul, Kelas, Enrollment, Waitlist};

class FRSController extends Controller
{
    private const MAX_SKS = 24;

    /* ========================
     * Master Data (opsional)
     * ======================== */
    public function saveMatkul(Request $r)
    {
        $data = $r->validate([
            'title' => 'required|string|max:100|unique:matkuls,title',
            // kalau mau input SKS via form, tambahkan:
            // 'sks'   => 'required|integer|min:1|max:24',
        ]);
        // Matkul::create($data); // kalau ada kolom sks di $fillable
        Matkul::create([
            'title' => $data['title'],
            // 'sks' => $data['sks'] ?? 3,
        ]);

        return back()->with('ok', 'Matkul disimpan.');
    }

    public function saveKelas(Request $r)
    {
        $data = $r->validate([
            'title'    => 'required|string|max:100|unique:kelas,title',
            // 'capacity' => 'nullable|integer|min:0|max:999'
        ]);

        Kelas::create([
            'title'    => $data['title'],
            // 'capacity' => $data['capacity'] ?? 40,
        ]);

        return back()->with('ok', 'Kelas disimpan.');
    }

    /* ========================
     * Enroll / Waitlist
     * ======================== */
    public function enroll(Request $r)
    {
        // 1) dukung value gabungan "matkulId|kelasId"
        if ($r->filled('pair')) {
            $r->validate(['pair' => ['required', 'regex:/^\d+\|\d+$/']]);
            [$matkulId, $kelasId] = array_map('intval', explode('|', $r->pair));
            $r->merge(['matkul_id' => $matkulId, 'kelas_id' => $kelasId]);
        }

        // 2) validasi final
        $data = $r->validate([
            'matkul_id' => 'required|integer|exists:matkuls,id',
            'kelas_id'  => 'required|integer|exists:kelas,id',
        ]);

        $userId  = auth()->id();
        $matkul  = Matkul::findOrFail($data['matkul_id']);
        $kelasId = (int) $data['kelas_id'];

        return DB::transaction(function () use ($userId, $matkul, $kelasId) {

            // lock kelas & hitung terisi
            $kelas = Kelas::whereKey($kelasId)->lockForUpdate()->firstOrFail();

            $already = Enrollment::where('user_id', $userId)
                        ->where('matkul_id', $matkul->id)
                        ->lockForUpdate()
                        ->first();

            $currentSks = (int) Enrollment::where('user_id', $userId)
                ->join('matkuls', 'enrollments.matkul_id', '=', 'matkuls.id')
                ->sum('matkuls.sks');

            $taken = (int) Enrollment::where('kelas_id', $kelasId)
                ->lockForUpdate()
                ->count();

            $capacity = (int) ($kelas->capacity ?? 0);
            $full = $capacity > 0 && $taken >= $capacity;

            /* CASE A: sudah punya matkul ini, hanya ganti kelas */
            if ($already) {
                if (!$full) {
                    $already->update(['kelas_id' => $kelasId]);
                    // bersihkan waitlist user untuk kelas ini (kalau ada)
                    Waitlist::where('user_id', $userId)->where('kelas_id', $kelasId)->delete();
                    return back()->with('ok', 'Kelas diperbarui.');
                }

                // target penuh → masukkan ke waitlist (idempotent)
                $wl = Waitlist::firstOrCreate(
                    ['user_id' => $userId, 'kelas_id' => $kelasId],  // kunci sesuai index unik
                    ['matkul_id' => $matkul->id]
                );

                $msg = $wl->wasRecentlyCreated
                    ? 'Kelas penuh. Kamu masuk waitlist untuk kelas tersebut.'
                    : 'Kelas penuh. Kamu sudah ada di waitlist kelas ini.';
                return back()->with('ok', $msg);
            }

            /* CASE B: ambil matkul baru */
            $after = $currentSks + (int) ($matkul->sks ?? 0);
            if ($after > self::MAX_SKS) {
                return back()->withErrors("Total SKS akan menjadi {$after}, melebihi batas " . self::MAX_SKS . " SKS.");
            }

            if (!$full) {
                Enrollment::create([
                    'user_id'   => $userId,
                    'matkul_id' => $matkul->id,
                    'kelas_id'  => $kelasId,
                ]);
                // bersihkan waitlist user untuk kelas ini (kalau ada)
                Waitlist::where('user_id', $userId)->where('kelas_id', $kelasId)->delete();

                return back()->with('ok', 'Berhasil ditambahkan ke FRS.');
            }

            // penuh → masuk waitlist (idempotent)
            $wl = Waitlist::firstOrCreate(
                ['user_id' => $userId, 'kelas_id' => $kelasId],
                ['matkul_id' => $matkul->id]
            );

            $msg = $wl->wasRecentlyCreated
                ? 'Kelas penuh. Kamu masuk waitlist. Kami akan promosikan otomatis ketika ada yang drop.'
                : 'Kelas penuh. Kamu sudah ada di waitlist kelas ini.';
            return back()->with('ok', $msg);
        });
    }

    /* ========================
     * Drop + promosi waitlist
     * ======================== */
    public function drop(Enrollment $enrollment)
    {
        abort_unless($enrollment->user_id === auth()->id(), 403);

        DB::transaction(function () use ($enrollment) {
            $kelasId  = $enrollment->kelas_id;
            $matkulId = $enrollment->matkul_id;

            // hapus enrollment saat ini
            $enrollment->delete();

            // cari antrian teratas untuk kelas yg sama
            $next = Waitlist::where('kelas_id', $kelasId)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->first();

            if ($next) {
                Enrollment::updateOrCreate(
                    ['user_id' => $next->user_id, 'matkul_id' => $next->matkul_id],
                    ['kelas_id' => $kelasId]
                );
                $next->delete();
                // (opsional) kirim notifikasi
            }
        });

        return back()->with('ok', 'Mata kuliah di-drop. Jika ada antrian, sudah dipromosikan otomatis.');
    }

    /* ========================
     * Submit (cek total SKS)
     * ======================== */
    public function submit(Request $r)
    {
        $userId = auth()->id();
        $total  = (int) Enrollment::where('user_id', $userId)
            ->join('matkuls', 'enrollments.matkul_id', '=', 'matkuls.id')
            ->sum('matkuls.sks');

        if ($total > self::MAX_SKS) {
            return back()->withErrors("Total SKS kamu {$total}. Batas maksimal adalah " . self::MAX_SKS . " SKS.");
        }

        // tempatkan logic "finalize" kalau perlu (status submitted, cetak PDF, dsb)
        return back()->with('ok', "FRS disubmit. Total SKS: {$total}.");
    }

    public function cancelWaitlist(\App\Models\Waitlist $waitlist)
{
    abort_unless($waitlist->user_id === auth()->id(), 403);
    $waitlist->delete();

    return back()->with('ok', 'Kamu keluar dari waitlist.');
}

// Hapus MATKUL (blokir kalau masih dipakai oleh enrollment atau waitlist)
public function destroyMatkul(\App\Models\Matkul $matkul)
{
    $enrollCount = \App\Models\Enrollment::where('matkul_id', $matkul->id)->count();
    $waitCount   = \App\Models\Waitlist::where('matkul_id', $matkul->id)->count();

    if ($enrollCount > 0 || $waitCount > 0) {
        return back()->withErrors("Tidak bisa hapus matkul karena masih dipakai (enroll: {$enrollCount}, waitlist: {$waitCount}).");
    }

    $matkul->delete();
    return back()->with('ok', 'Matkul dihapus.');
}

// Hapus KELAS (blokir kalau masih dipakai)
public function destroyKelas(\App\Models\Kelas $kelas)
{
    $enrollCount = \App\Models\Enrollment::where('kelas_id', $kelas->id)->count();
    $waitCount   = \App\Models\Waitlist::where('kelas_id', $kelas->id)->count();

    if ($enrollCount > 0 || $waitCount > 0) {
        return back()->withErrors("Tidak bisa hapus kelas karena masih dipakai (enroll: {$enrollCount}, waitlist: {$waitCount}).");
    }

    $kelas->delete();
    return back()->with('ok', 'Kelas dihapus.');
}


}

