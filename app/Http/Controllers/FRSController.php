<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Matkul, Kelas, Enrollment, Waitlist};

class FRSController extends Controller
{
    private const MAX_SKS = 24;

    // add matkul
    public function saveMatkul(Request $r) {
        $data = $r->validate([
            'title' => 'required|string|max:100|unique:matkuls,title'
        ]);
        Matkul::create($data);
        return back()->with('ok', 'Matkul disimpan.');
    }

    // add kelas
    public function saveKelas(Request $r) {
        $data = $r->validate([
            'title' => 'required|string|max:100|unique:kelas,title'
        ]);
        Kelas::create($data);
        return back()->with('ok', 'Kelas disimpan.');
    }

    // ENROLL (support pair & limit SKS & waitlist)
    public function enroll(Request $r)
    {
        if ($r->filled('pair')) {
            $r->validate(['pair' => ['required','regex:/^\d+\|\d+$/']]);
            [$matkulId, $kelasId] = array_map('intval', explode('|', $r->pair));
            $r->merge(['matkul_id' => $matkulId, 'kelas_id' => $kelasId]);
        }

        $data = $r->validate([
            'matkul_id' => 'required|integer|exists:matkuls,id',
            'kelas_id'  => 'required|integer|exists:kelas,id',
        ]);

        $userId  = auth()->id();
        $matkul  = Matkul::findOrFail($data['matkul_id']);
        $kelasId = (int)$data['kelas_id'];

        return DB::transaction(function () use ($userId, $matkul, $kelasId) {

            // lock kelas + count enrolls
            $kelas = Kelas::whereKey($kelasId)->lockForUpdate()->firstOrFail();

            $already = Enrollment::where('user_id',$userId)
                        ->where('matkul_id',$matkul->id)
                        ->lockForUpdate()
                        ->first();

            // total sks saat ini
            $currentSks = (int) Enrollment::where('user_id', $userId)
                ->join('matkuls','enrollments.matkul_id','=','matkuls.id')
                ->sum('matkuls.sks');

            // hitung kursi terisi pada kelas target
            $taken = (int) Enrollment::where('kelas_id', $kelasId)->lockForUpdate()->count();
            $full  = $taken >= (int)$kelas->capacity;

            // CASE 1: user sudah ambil matkul ini, hanya ganti kelas
            if ($already) {
                if (!$full) {
                    // update ke kelas baru
                    $already->update(['kelas_id' => $kelasId]);
                    // pastikan kalau ada di waitlist kelas ini, dihapus
                    Waitlist::where('user_id',$userId)->where('kelas_id',$kelasId)->delete();
                    return back()->with('ok', 'Kelas diperbarui.');
                }
                // target penuh → masuk waitlist kalau belum
                Waitlist::firstOrCreate([
                    'user_id'  => $userId,
                    'matkul_id'=> $matkul->id,
                    'kelas_id' => $kelasId,
                ]);
                return back()->with('ok', 'Kelas penuh. Kamu masuk waitlist untuk kelas tersebut.');
            }

            // CASE 2: ambil matkul baru → cek limit SKS
            $after = $currentSks + (int)($matkul->sks ?? 0);
            if ($after > self::MAX_SKS) {
                return back()->withErrors("Total SKS akan menjadi {$after}, melebihi batas ".self::MAX_SKS." SKS.");
            }

            if (!$full) {
                // kursi tersedia → enroll sekarang
                Enrollment::create([
                    'user_id'   => $userId,
                    'matkul_id' => $matkul->id,
                    'kelas_id'  => $kelasId,
                ]);
                // jaga-jaga bersihkan waitlist kelas ini untuk user
                Waitlist::where('user_id',$userId)->where('kelas_id',$kelasId)->delete();
                return back()->with('ok', 'Berhasil ditambahkan ke FRS.');
            }

            // penuh → masuk waitlist
            Waitlist::firstOrCreate([
                'user_id'  => $userId,
                'matkul_id'=> $matkul->id,
                'kelas_id' => $kelasId,
            ]);
            return back()->with('ok', 'Kelas penuh. Kamu masuk waitlist. Kami akan promosikan otomatis ketika ada yang drop.');
        });
    }

    // DROP + PROMOSI WAITLIST
    public function drop(Enrollment $enrollment)
    {
        abort_unless($enrollment->user_id === auth()->id(), 403);

        DB::transaction(function () use ($enrollment) {
            $kelasId  = $enrollment->kelas_id;
            $matkulId = $enrollment->matkul_id;

            // hapus enrollment
            $enrollment->delete();

            // cari antrian teratas untuk kelas yang sama
            $next = Waitlist::where('kelas_id', $kelasId)
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->first();

            if ($next) {
                // buat enrollment untuk orang di antrian teratas
                Enrollment::updateOrCreate(
                    ['user_id'=>$next->user_id,'matkul_id'=>$next->matkul_id],
                    ['kelas_id'=>$kelasId]
                );
                // hapus dari antrian
                $next->delete();
                // (opsional) kirim notifikasi, email, dsb. Di demo ini cukup otomatis masuk.
            }
        });

        return back()->with('ok', 'Mata kuliah di-drop. Jika ada antrian, sudah dipromosikan otomatis.');
    }

    // SUBMIT (limit SKS final)
    public function submit(Request $r)
    {
        $userId = auth()->id();
        $total  = (int) Enrollment::where('user_id', $userId)
            ->join('matkuls','enrollments.matkul_id','=','matkuls.id')
            ->sum('matkuls.sks');

        if ($total > self::MAX_SKS) {
            return back()->withErrors("Total SKS kamu {$total}. Batas maksimal adalah ".self::MAX_SKS." SKS.");
        }

        return back()->with('ok', "FRS disubmit. Total SKS: {$total}.");
    }
}
