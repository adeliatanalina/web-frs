<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Web-FRS</title>
  <style>
    :root{--muted:#f5f5f5;--ok:#e7f6ec;--okb:#b8e0c7;--okc:#0b6b2f;--err:#fdeeee;--errb:#f3c2c2;--errc:#8a1f1f}
    body{font-family:system-ui,-apple-system,Arial,sans-serif;line-height:1.45;padding:16px}
    h2{margin-top:24px} .box{margin:12px 0 24px}
    .row{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
    select,input,button{padding:6px 10px}
    .alert{padding:8px 10px;border-radius:6px;margin:8px 0}
    .alert-success{background:var(--ok);color:var(--okc);border:1px solid var(--okb)}
    .alert-error{background:var(--err);color:var(--errc);border:1px solid var(--errb)}
    small.text-danger{color:#b00020}
    table{border-collapse:collapse;width:100%}
    th,td{border:1px solid #ddd;padding:8px;text-align:left;vertical-align:top}
    thead th{background:var(--muted)}
    .actions form{display:inline}
    .pill{display:inline-block;padding:2px 8px;border-radius:999px;border:1px solid #ddd;font-size:12px;color:#555}
  </style>
</head>
<body>

  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-error">
      @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
    </div>
  @endif

  @guest
    <h2>Web-FRS</h2>
    @php
      // Halaman aktif: 'register' (default) atau 'login'
      $activePage = request()->get('page') === 'login' ? 'login' : 'register';
    @endphp

    {{-- REGISTER --}}
    <div class="box" @if($activePage !== 'register') style="display:none;" @endif>
      <h3>Register</h3>
      <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="row">
          <input name="name" type="text" placeholder="name" value="{{ old('name') }}">
          <input name="email" type="text"
                 placeholder="contoh: 5024241033@student.its.ac.id"
                 value="{{ old('email') }}">
          <input name="password" type="password" placeholder="password">
          <button type="submit">Register</button>
        </div>
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
      </form>
      <p style="margin-top:10px">Sudah punya akun? <a href="?page=login">Login di sini</a></p>
    </div>

    {{-- LOGIN --}}
    <div class="box" @if($activePage !== 'login') style="display:none;" @endif>
      <h3>Login</h3>
      <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="row">
          <input name="loginname" type="text"
                 placeholder="contoh: 5024241033@student.its.ac.id"
                 value="{{ old('loginname') }}">
          <input name="loginpassword" type="password" placeholder="password">
          <button type="submit">Login</button>
        </div>
        @error('loginname') <small class="text-danger">{{ $message }}</small> @enderror
        @error('loginpassword') <small class="text-danger">{{ $message }}</small> @enderror
      </form>
      <p style="margin-top:10px">Belum punya akun? <a href="?page=register">Register di sini</a></p>
    </div>
  @endguest

  @auth
    @php
      // === SIMULASI IPS/IPK (1.00–4.00) DISIMPAN DI SESSION ===
      if (session()->has('sim_gpa')) {
        $gpa = session('sim_gpa');
      } else {
        $gpa = round(mt_rand(100, 400) / 100, 2); // 1.00–4.00, 2 desimal
        session(['sim_gpa' => $gpa]);
      }

      // === Mapping IPS -> Maks SKS sesuai tabel ===
      if ($gpa < 2.50)      $MAX_SKS = 18;
      elseif ($gpa < 3.00)  $MAX_SKS = 20;
      elseif ($gpa < 3.50)  $MAX_SKS = 22;
      else                  $MAX_SKS = 24;
    @endphp

    {{-- IDENTITAS USER --}}
    <p style="margin:6px 0"><strong>{{ auth()->user()->name ?? 'User' }}</strong></p>
    <p style="margin:6px 0"><span class="pill">NRP: {{ auth()->user()->nrp ?? '—' }}</span></p>
    <p style="margin:6px 0">
      <span class="pill">Sim. IPS: {{ number_format($gpa, 2) }}</span>
      &nbsp; <small style="color:#555">Maks SKS sesuai IPS: <strong>{{ $MAX_SKS }} SKS</strong></small>
      {{-- Tombol kecil untuk regenerate nilai simulasi (opsional) --}}

      @php
        if (request()->has('regen')) {
          session()->forget('sim_gpa');
          echo '<meta http-equiv="refresh" content="0;url='.e(url()->current()).'">';
        }
      @endphp
    </p>

    <form action="{{ route('logout') }}" method="POST" class="box">
      @csrf
      <button type="submit">log out</button>
    </form>

    @php
      // ===== data untuk dropdown gabungan =====
      $matkuls = \App\Models\Matkul::orderBy('title')->get();
      $kelass  = \App\Models\Kelas::orderBy('title')->get()->keyBy('id');

      // hitung seat terisi per kelas (sekali query)
      $takenPerKelas = \App\Models\Enrollment::selectRaw('kelas_id, COUNT(*) as c')
          ->groupBy('kelas_id')->pluck('c','kelas_id');

      // helper "nama jam"
      if (!function_exists('timeSlotName')) {
        function timeSlotName($hhmm) {
          if (!$hhmm) return '';
          [$h,$m] = array_pad(explode(':', $hhmm), 2, '0');
          $min = ((int)$h)*60 + (int)$m;
          if ($min < 11*60) return 'Pagi';
          if ($min < 16*60) return 'Siang';
          if ($min < 19*60) return 'Sore';
          return 'Malam';
        }
      }

      // opsi gabungan: VALUE = "matkulId|kelasId"
      $pairs = [];
      foreach ($matkuls as $m) {
        foreach ($kelass as $k) {
          $cap   = (int)($k->capacity ?? 0);
          $taken = (int)($takenPerKelas[$k->id] ?? 0);
          $isFull = $cap > 0 && $taken >= $cap;

          $label = $m->title.' ('.($m->sks ?? '-').' SKS) — '.$k->title.' · ';
          $label .= "Kursi: {$taken}/".($cap ?: '—');
          if ($isFull) $label .= ' (Penuh — akan masuk waitlist)';

          if (isset($k->start_time,$k->end_time) && $k->start_time && $k->end_time) {
            $label .= ' · '.$k->start_time.'–'.$k->end_time.' ('.timeSlotName($k->start_time).')';
          }

          $pairs[] = (object)[ 'value' => $m->id.'|'.$k->id, 'label' => $label ];
        }
      }
    @endphp

    <h2>Ambil Matkul (Satu Pilihan)</h2>
    <div class="box">
      <form action="{{ route('frs.enroll') }}" method="POST" class="row">
        @csrf

        <label for="pair">Matkul + Kelas</label>
        <select id="pair" name="pair" required {{ empty($pairs) ? 'disabled' : '' }}>
          @if(empty($pairs))
            <option>Belum ada data matkul/kelas.</option>
          @else
            <option value="" disabled selected>-- Pilih --</option>
            @foreach($pairs as $p)
              <option value="{{ $p->value }}">{{ $p->label }}</option>
            @endforeach
          @endif
        </select>
        @error('pair') <small class="text-danger">{{ $message }}</small> @enderror

        <button type="submit" {{ empty($pairs) ? 'disabled' : '' }}>
          Simpan Pilihan
        </button>
      </form>

      <p style="margin-top:8px;color:#555">
        <span class="pill">Catatan</span> Jika kelas penuh, pilihanmu otomatis masuk <strong>waitlist</strong>.
        Saat ada yang drop, antrian teratas akan otomatis masuk FRS.
      </p>
    </div>

    <div class="box">
      <h3>FRS-ku</h3>
      @php
        $enrolls = \App\Models\Enrollment::with(['matkul','kelas'])
                   ->where('user_id', auth()->id())->get();

        $totalSks = 0;
        foreach ($enrolls as $e) { $totalSks += (int)($e->matkul->sks ?? 0); }

        // $MAX_SKS sudah dihitung dari simulasi IPS di atas (fallback 24 kalau belum)
        $remaining = max(0, ($MAX_SKS ?? 24) - $totalSks);
      @endphp


      <table>
        <thead>
          <tr>
            <th style="width:60px">#</th>
            <th>Matkul</th>
            <th style="width:90px">SKS</th>
            <th>Kelas</th>
            <th style="width:180px">Diambil Pada</th>
            <th style="width:120px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($enrolls as $i => $e)
            @php $sks = (int)($e->matkul->sks ?? 0); @endphp
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $e->matkul->title }}</td>
              <td>{{ $sks ?: '-' }}</td>
              <td>
                {{ $e->kelas->title }}
                @if(isset($e->kelas->capacity))
                  <div class="pill" style="margin-top:4px">
                    Kapasitas:
                    {{ (\App\Models\Enrollment::where('kelas_id',$e->kelas->id)->count()) }}
                    /
                    {{ $e->kelas->capacity }}
                  </div>
                @endif
              </td>
              <td>{{ $e->created_at->format('Y-m-d H:i') }}</td>
              <td class="actions">
                <form action="{{ route('frs.drop', $e->id) }}" method="POST"
                      onsubmit="return confirm('Yakin drop {{ $e->matkul->title }} — {{ $e->kelas->title }}?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit">Drop</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6">Belum ada mata kuliah yang diambil.</td></tr>
          @endforelse
          <tr>
            <td colspan="6" style="text-align:right"><strong>Total SKS: {{ $totalSks }}</strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    {{-- =================== WAITLIST =================== --}}
    <div class="box">
      <h3>Waitlist-ku</h3>
      @php
        $myWaits = \App\Models\Waitlist::with(['matkul','kelas'])
                   ->where('user_id', auth()->id())
                   ->orderBy('created_at','asc')
                   ->get();
      @endphp
      <table>
        <thead>
          <tr>
            <th style="width:60px">#</th>
            <th>Matkul</th>
            <th>Kelas</th>
            <th>Posisi</th>
            <th style="width:220px">Keterangan</th>
            <th style="width:180px">Di-antri Pada</th>
            <th style="width:100px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($myWaits as $i => $w)
            @php
              $pos = \App\Models\Waitlist::where('kelas_id',$w->kelas_id)
                      ->where('matkul_id',$w->matkul_id)
                      ->where('created_at','<=',$w->created_at)->count();
              $cap   = (int)($w->kelas->capacity ?? 0);
              $taken = (int)(\App\Models\Enrollment::where('kelas_id',$w->kelas_id)->count());
              $left  = max(0, $cap - $taken);
            @endphp
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $w->matkul->title ?? '-' }}</td>
              <td>{{ $w->kelas->title ?? '-' }}</td>
              <td>#{{ $pos }}</td>
              <td>
                Kapasitas: {{ $taken }}/{{ $cap ?: '—' }}<br>
                @if($left>0)
                  <span class="pill">Ada {{ $left }} kursi kosong — segera dipromosikan otomatis</span>
                @else
                  Menunggu ada yang drop…
                @endif
              </td>
              <td>{{ $w->created_at->format('Y-m-d H:i') }}</td>
              <td class="actions">
                <form action="{{ route('waitlist.cancel', $w->id) }}" method="POST"
                      onsubmit="return confirm('Keluar dari waitlist {{ $w->matkul->title }} — {{ $w->kelas->title }}?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit">Cancel</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7">Tidak ada antrian.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- =================== PANEL DEV: MASTER DATA =================== --}}
    <div class="box">
      <h2>Matkul</h2>
      <form action="{{ route('matkul.create') }}" method="POST" class="row">
        @csrf
        <input type="text" name="title" placeholder="contoh: Sistem Basis Data" required>
        <button type="submit">Tambah Matkul</button>
      </form>
      @error('title') <div class="alert alert-error">{{ $message }}</div> @enderror

      @php $matkulsAdmin = \App\Models\Matkul::orderBy('title')->get(); @endphp
      <table style="margin-top:10px">
        <thead>
          <tr>
            <th style="width:60px">#</th>
            <th>Nama Matkul</th>
            <th style="width:120px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($matkulsAdmin as $i => $m)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $m->title }}</td>
              <td class="actions">
                <form action="{{ route('matkul.destroy', $m->id) }}" method="POST"
                      onsubmit="return confirm('Hapus matkul {{ $m->title }}?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3">Belum ada data matkul.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="box">
      <h2>Kelas</h2>
      <form action="{{ route('kelas.create') }}" method="POST" class="row">
        @csrf
        <input type="text" name="title" placeholder="contoh: IF 107" required>
        <button type="submit">Tambah Kelas</button>
      </form>
      @error('title') <div class="alert alert-error">{{ $message }}</div> @enderror

      @php $kelassAdmin = \App\Models\Kelas::orderBy('title')->get(); @endphp
      <table style="margin-top:10px">
        <thead>
          <tr>
            <th style="width:60px">#</th>
            <th>Kode Kelas</th>
            <th style="width:120px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($kelassAdmin as $i => $k)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $k->title }}</td>
              <td class="actions">
                <form action="{{ route('kelas.destroy', $k->id) }}" method="POST"
                      onsubmit="return confirm('Hapus kelas {{ $k->title }}?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3">Belum ada data kelas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @endauth
</body>
</html>
