<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Web-FRS</title>
  <style>
    :root{
      --bg:#ffffff; --text:#1f2937; --muted:#f8fafc; --border:#e5e7eb;
      --primary:#2563eb; --primary-600:#1d4ed8;
      --ok:#e7f6ec; --okb:#b8e0c7; --okc:#0b6b2f;
      --err:#fdeeee; --errb:#f3c2c2; --errc:#8a1f1f;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background:var(--bg); color:var(--text);
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      line-height:1.5;
    }
    .container{max-width:980px; margin:28px auto 64px; padding:0 16px}

    .header{position:sticky; top:12px; z-index:5; padding:8px 0 14px; background:transparent}
    .header-inner{position:relative; display:flex; justify-content:center; align-items:center; min-height:44px}
    .header h1{margin:0; font-size:26px; font-weight:700; letter-spacing:.2px}
    .header .actions-right{position:absolute; right:0; top:0}
    .header button{
      padding:10px 14px; border:1px solid var(--primary); background:var(--primary);
      color:#fff; font-weight:600; border-radius:8px; cursor:pointer;
    }
    .header button:hover{background:var(--primary-600); border-color:var(--primary-600)}

    h2{margin:0 0 8px; font-size:20px}
    h3{margin:0 0 8px; font-size:18px}
    .box{background:#fff; border:1px solid var(--border); border-radius:10px; padding:16px; margin:16px 0 24px}
    .muted{background:var(--muted); border:1px solid var(--border); border-radius:8px; padding:8px 10px}

    .grid{display:grid; gap:16px}
    .grid-2{grid-template-columns:1fr}
    @media (min-width: 920px){ .grid-2{grid-template-columns:1fr 1fr} }

    .row{display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end}
    label{font-size:12px; display:block; color:#475569}
    .field{display:flex; flex-direction:column; gap:6px; min-width:220px}
    input,select,button{font:inherit; border-radius:8px; outline:none}
    input,select{border:1px solid var(--border); background:#fff; color:#111827; padding:10px 12px}
    input:focus,select:focus{border-color:var(--primary); box-shadow:0 0 0 2px rgba(37,99,235,.15)}
    button{padding:10px 14px; border:1px solid var(--primary); background:var(--primary); color:#fff; font-weight:600; cursor:pointer}
    button:hover{background:var(--primary-600); border-color:var(--primary-600)}
    button:disabled{opacity:.6; cursor:not-allowed}

    .alert{padding:10px 12px; border-radius:8px; border:1px solid transparent; margin:12px 0}
    .alert-success{background:var(--ok); color:var(--okc); border-color:var(--okb)}
    .alert-error{background:var(--err); color:var(--errc); border-color:var(--errb)}
    small.text-danger{color:#b91c1c}

    table{width:100%; border-collapse:collapse}
    thead th{background:var(--muted); border:1px solid var(--border); padding:10px; text-align:left; font-size:13px; color:#334155}
    tbody td{border:1px solid var(--border); padding:10px; vertical-align:top}
    .actions form{display:inline}

    .pill{display:inline-block; padding:4px 10px; border-radius:999px; border:1px solid var(--border); font-size:13px; color:#334155; background:#fff}
    .mt-8{margin-top:8px} .mt-12{margin-top:12px} .ml-auto{margin-left:auto}
    .right{text-align:right}
    a{color:var(--primary); text-decoration:none} a:hover{text-decoration:underline}

    .auth-wrap{max-width:640px; margin:0 auto}
    .headline{font-size:24px; font-weight:700}
    .nrp-big{font-size:16px}
    .stats-row{display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-top:8px}
  </style>
</head>
<body>
<div class="container">

  <div class="header">
    <div class="header-inner">
      <h1>Web-FRS</h1>
      @auth
        <div class="actions-right">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
          </form>
        </div>
      @endauth
    </div>
  </div>

  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
  @endif

  @guest
    @php $activePage = request()->get('page') === 'login' ? 'login' : 'register'; @endphp

    <div class="auth-wrap">
      <div class="box" @if($activePage !== 'register') style="display:none" @endif>
        <h2 style="text-align:center">Register</h2>
        <form action="{{ route('register') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field" style="flex:1 1 100%">
              <label for="name">Nama</label>
              <input id="name" name="name" type="text" placeholder="Nama lengkap" value="{{ old('name') }}">
            </div>
            <div class="field" style="flex:1 1 100%">
              <label for="email">Email</label>
              <input id="email" name="email" type="text" placeholder="50242410xx@student.its.ac.id" value="{{ old('email') }}">
            </div>
            <div class="field" style="flex:1 1 100%">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" placeholder="Minimal 8 karakter">
            </div>
            <button type="submit" class="ml-auto">Buat Akun</button>
          </div>
          @error('name') <small class="text-danger">{{ $message }}</small> @enderror
          @error('email') <small class="text-danger">{{ $message }}</small> @enderror
          @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </form>
        <div class="muted mt-12" style="text-align:center">Sudah punya akun? <a href="?page=login">Masuk di sini</a>.</div>
      </div>

      <div class="box" @if($activePage !== 'login') style="display:none" @endif>
        <h2 style="text-align:center">Login</h2>
        <form action="{{ route('login.submit') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field" style="flex:1 1 100%">
              <label for="loginname">Username / Email</label>
              <input id="loginname" name="loginname" type="text" placeholder="nama / email" value="{{ old('loginname') }}">
            </div>
            <div class="field" style="flex:1 1 100%">
              <label for="loginpassword">Password</label>
              <input id="loginpassword" name="loginpassword" type="password" placeholder="password">
            </div>
            <button type="submit" class="ml-auto">Masuk</button>
          </div>
          @error('loginname') <small class="text-danger">{{ $message }}</small> @enderror
          @error('loginpassword') <small class="text-danger">{{ $message }}</small> @enderror
        </form>
        <div class="muted mt-12" style="text-align:center">Belum punya akun? <a href="?page=register">Daftar di sini</a>.</div>
      </div>
    </div>
  @endguest

  @auth
    @php
      if (session()->has('sim_gpa')) { $gpa = session('sim_gpa'); }
      else { $gpa = round(mt_rand(100, 400) / 100, 2); session(['sim_gpa' => $gpa]); }
      if ($gpa < 2.50)      $MAX_SKS = 18;
      elseif ($gpa < 3.00)  $MAX_SKS = 20;
      elseif ($gpa < 3.50)  $MAX_SKS = 22;
      else                  $MAX_SKS = 24;
    @endphp

    <div class="box">
      <div class="headline">{{ auth()->user()->name ?? 'User' }}</div>
      <div class="stats-row">
        <span class="pill nrp-big">NRP: {{ auth()->user()->nrp ?? '—' }}</span>
        <span class="pill">IPS: {{ number_format($gpa, 2) }}</span>
        <span class="pill">Maks SKS: {{ $MAX_SKS }}</span>
      </div>
    </div>

    @php
      $matkuls = \App\Models\Matkul::orderBy('title')->get();
      $kelass  = \App\Models\Kelas::orderBy('title')->get()->keyBy('id');
      $takenPerKelas = \App\Models\Enrollment::selectRaw('kelas_id, COUNT(*) as c')->groupBy('kelas_id')->pluck('c','kelas_id');
      if (!function_exists('timeSlotName')) {
        function timeSlotName($hhmm){
          if (!$hhmm) return '';
          [$h,$m] = array_pad(explode(':',$hhmm),2,'0');
          $min=((int)$h)*60+(int)$m;
          if ($min<660) return 'Pagi';
          if ($min<960) return 'Siang';
          if ($min<1140) return 'Sore';
          return 'Malam';
        }
      }
      $pairs=[];
      foreach($matkuls as $m){
        foreach($kelass as $k){
          $cap=(int)($k->capacity ?? 0);
          $taken=(int)($takenPerKelas[$k->id] ?? 0);
          $isFull = $cap>0 && $taken >= $cap;
          $label = $m->title.' ('.($m->sks ?? '-').' SKS) — '.$k->title.' · ';
          $label .= "Kursi: {$taken}/".($cap ?: '—');
          if ($isFull) $label .= ' (Penuh → waitlist)';
          if (!empty($k->start_time) && !empty($k->end_time)){
            $label .= ' · '.$k->start_time.'–'.$k->end_time.' ('.timeSlotName($k->start_time).')';
          }
          $pairs[] = (object)['value'=>$m->id.'|'.$k->id,'label'=>$label];
        }
      }
    @endphp

    <div class="grid grid-2">
      <div class="box">
        <h2>Ambil Matkul</h2>
        <form action="{{ route('frs.enroll') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field" style="min-width:320px">
              <label for="pair">Matkul + Kelas</label>
              <select id="pair" name="pair" required {{ empty($pairs) ? 'disabled' : '' }}>
                @if(empty($pairs))
                  <option>Belum ada data matkul/kelas.</option>
                @else
                  <option value="" disabled selected>-- Pilih salah satu --</option>
                  @foreach($pairs as $p) <option value="{{ $p->value }}">{{ $p->label }}</option> @endforeach
                @endif
              </select>
            </div>
            <button type="submit" {{ empty($pairs) ? 'disabled' : '' }}>Simpan</button>
          </div>
          @error('pair') <small class="text-danger">{{ $message }}</small> @enderror
        </form>
        <div class="muted mt-12">
          Jika kelas penuh, pilihanmu otomatis masuk <strong>waitlist</strong>. Saat ada yang drop, antrian teratas akan masuk FRS.
        </div>
      </div>

      <div class="box">
        <h2>Waitlist-ku</h2>
        @php
          $myWaits = \App\Models\Waitlist::with(['matkul','kelas'])
                    ->where('user_id', auth()->id())
                    ->orderBy('created_at','asc')->get();
        @endphp
        <table class="mt-12">
          <thead>
            <tr>
              <th style="width:48px">#</th>
              <th>Matkul</th>
              <th>Kelas</th>
              <th style="width:72px">Posisi</th>
              <th>Keterangan</th>
              <th style="width:160px">Di-antri</th>
              <th style="width:100px">Aksi</th>
            </tr>
          </thead>
          <tbody>
          @forelse($myWaits as $i => $w)
            @php
              $pos = \App\Models\Waitlist::where('kelas_id',$w->kelas_id)
                      ->where('matkul_id',$w->matkul_id)
                      ->where('created_at','<=',$w->created_at)->count();
              $cap = (int)($w->kelas->capacity ?? 0);
              $taken = (int)(\App\Models\Enrollment::where('kelas_id',$w->kelas_id)->count());
              $left = max(0, $cap - $taken);
            @endphp
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $w->matkul->title ?? '-' }}</td>
              <td>{{ $w->kelas->title ?? '-' }}</td>
              <td>#{{ $pos }}</td>
              <td class="small">
                Kapasitas: {{ $taken }}/{{ $cap ?: '—' }} —
                @if($left>0) {{ $left }} kursi kosong (menunggu promosi)
                @else Menunggu ada yang drop @endif
              </td>
              <td>{{ $w->created_at->format('Y-m-d H:i') }}</td>
              <td class="actions">
                <form action="{{ route('waitlist.cancel', $w->id) }}" method="POST"
                      onsubmit="return confirm('Batalkan waitlist {{ $w->matkul->title }} — {{ $w->kelas->title }}?')">
                  @csrf @method('DELETE')
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
    </div>

    <div class="box">
      <h2>FRS-ku</h2>
      @php
        $enrolls = \App\Models\Enrollment::with(['matkul','kelas'])->where('user_id', auth()->id())->get();
        $totalSks = 0; foreach($enrolls as $e){ $totalSks += (int)($e->matkul->sks ?? 0); }
        $remaining = max(0, ($MAX_SKS ?? 24) - $totalSks);
      @endphp
      <div class="row mt-12">
        <span class="pill">Total SKS: {{ $totalSks }} / {{ $MAX_SKS }}</span>
        <span class="pill">Sisa SKS: {{ $remaining }}</span>
        <form action="{{ route('frs.submit') }}" method="POST" class="ml-auto">
          @csrf
          <button type="submit" {{ $totalSks > $MAX_SKS ? 'disabled' : '' }}>Submit FRS</button>
        </form>
      </div>

      <table class="mt-12">
        <thead>
          <tr>
            <th style="width:48px">#</th>
            <th>Matkul</th>
            <th style="width:72px">SKS</th>
            <th>Kelas</th>
            <th style="width:160px">Diambil</th>
            <th style="width:100px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($enrolls as $i => $e)
          @php $sks=(int)($e->matkul->sks ?? 0); @endphp
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $e->matkul->title }}</td>
            <td>{{ $sks ?: '-' }}</td>
            <td>
              {{ $e->kelas->title }}
              @if(isset($e->kelas->capacity))
                <div class="mt-8 small">Kapasitas:
                  {{ (\App\Models\Enrollment::where('kelas_id',$e->kelas->id)->count()) }} / {{ $e->kelas->capacity }}
                </div>
              @endif
            </td>
            <td>{{ $e->created_at->format('Y-m-d H:i') }}</td>
            <td class="actions">
              <form action="{{ route('frs.drop', $e->id) }}" method="POST"
                    onsubmit="return confirm('Drop {{ $e->matkul->title }} — {{ $e->kelas->title }}?')">
                @csrf @method('DELETE')
                <button type="submit">Drop</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6">Belum ada mata kuliah yang diambil.</td></tr>
        @endforelse
        <tr><td colspan="6" class="right"><strong>Total SKS: {{ $totalSks }}</strong></td></tr>
        </tbody>
      </table>
    </div>

    <div class="grid grid-2">
      
      <div class="box">
        <h2>Matkul</h2>
        <form action="{{ route('matkul.create') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field">
              <label for="mkTitle">Nama Matkul</label>
              <input id="mkTitle" type="text" name="title" placeholder="Sistem Basis Data" required>
            </div>
            <button type="submit" class="ml-auto">Tambah</button>
          </div>
        </form>
        @error('title') <div class="alert alert-error mt-12">{{ $message }}</div> @enderror

        @php $matkulsAdmin = \App\Models\Matkul::orderBy('title')->get(); @endphp
        <table class="mt-12">
          <thead><tr><th style="width:48px">#</th><th>Nama Matkul</th><th style="width:100px">Aksi</th></tr></thead>
          <tbody>
            @forelse($matkulsAdmin as $i => $m)
              <tr>
                <td>{{ $i+1 }}</td><td>{{ $m->title }}</td>
                <td class="actions">
                  <form action="{{ route('matkul.destroy', $m->id) }}" method="POST"
                        onsubmit="return confirm('Hapus matkul {{ $m->title }}?')">
                    @csrf @method('DELETE')
                    <button type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty <tr><td colspan="3">Belum ada data matkul.</td></tr> @endforelse
          </tbody>
        </table>
      </div>

      <div class="box">
        <h2>Kelas</h2>
        <form action="{{ route('kelas.create') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field">
              <label for="kelasTitle">Kode Kelas</label>
              <input id="kelasTitle" type="text" name="title" placeholder="IF 107" required>
            </div>
            <button type="submit" class="ml-auto">Tambah</button>
          </div>
        </form>
        @error('title') <div class="alert alert-error mt-12">{{ $message }}</div> @enderror

        @php $kelassAdmin = \App\Models\Kelas::orderBy('title')->get(); @endphp
        <table class="mt-12">
          <thead><tr><th style="width:48px">#</th><th>Kode Kelas</th><th style="width:100px">Aksi</th></tr></thead>
          <tbody>
            @forelse($kelassAdmin as $i => $k)
              <tr>
                <td>{{ $i+1 }}</td><td>{{ $k->title }}</td>
                <td class="actions">
                  <form action="{{ route('kelas.destroy', $k->id) }}" method="POST"
                        onsubmit="return confirm('Hapus kelas {{ $k->title }}?')">
                    @csrf @method('DELETE')
                    <button type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty <tr><td colspan="3">Belum ada data kelas.</td></tr> @endforelse
          </tbody>
        </table>
      </div>
    </div>

  
  @endauth

</div>
</body>
</html>
