<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Web-FRS</title>

  <style>
    :root{
      --bg:#f8fafc;              /* background utama */
      --panel:#ffffff;           /* card / box */
      --panel-2:#f1f5f9;         /* panel lembut */
      --text:#1e293b;            /* slate-800 */
      --muted:#f9fafb;           /* abu lembut */
      --soft:#f1f5f9;
      --border:#e2e8f0;          /* abu border */
      --shadow:rgba(15,23,42,0.08);

      --primary:#2563eb;         /* biru */
      --primary-600:#1d4ed8;
      --ring:rgba(37,99,235,0.25);

      --ok:#dcfce7; --okb:#16a34a; --okc:#14532d;
      --err:#fee2e2; --errb:#ef4444; --errc:#7f1d1d;

      --radius:14px;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      color:var(--text);
      background:
        radial-gradient(1000px 600px at 0% 0%, #f1f5f9 5%, transparent 50%),
        radial-gradient(800px 500px at 100% 0%, #e2e8f0 5%, transparent 50%),
        linear-gradient(180deg,#ffffff,#f8fafc);
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      line-height:1.6;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    .container{max-width:1000px; margin:28px auto 64px; padding:0 18px}

    /* Header */
    .header{
      position:sticky; top:0; z-index:30;
      backdrop-filter:blur(6px);
      background:rgba(255,255,255,0.7);
      border-bottom:1px solid var(--border);
    }
    .header-inner{
      display:flex; align-items:center; gap:12px;
      padding:16px 0;
    }
    .brand{display:flex; align-items:center; gap:12px;}
    .logo{
      width:34px;height:34px;border-radius:10px;
      background:linear-gradient(135deg, var(--primary), #60a5fa);
      box-shadow:0 4px 16px rgba(37,99,235,0.3);
    }
    .header h1{margin:0; font-size:22px; letter-spacing:.3px; font-weight:700}
    .spacer{flex:1}
    .actions-right{display:flex; align-items:center; gap:8px}

    /* Buttons */
    .btn{
      appearance:none; cursor:pointer;
      padding:10px 16px; border-radius:12px;
      border:1px solid var(--primary-600);
      background:linear-gradient(180deg, var(--primary), var(--primary-600));
      color:#fff; font-weight:600; letter-spacing:.2px;
      box-shadow:0 3px 10px rgba(37,99,235,0.3);
      transition:transform .08s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn:hover{filter:brightness(1.05); transform:translateY(-1px);}
    .btn.secondary{
      border-color:var(--border);
      background:linear-gradient(180deg,#f8fafc,#e2e8f0);
      color:#334155;
      box-shadow:0 2px 6px rgba(0,0,0,0.05);
    }
    .btn:disabled{opacity:.6; cursor:not-allowed;}

    /* Card / Section */
    .section{
      background:var(--panel);
      border:1px solid var(--border);
      border-radius:var(--radius);
      padding:20px; margin:18px 0 26px;
      box-shadow:0 4px 16px var(--shadow);
    }
    .section h2{margin:0 0 6px; font-size:20px; font-weight:700;}
    .section .sub{color:#64748b; font-size:13px;}

    /* Grid */
    .grid{display:grid; gap:18px}
    .grid-2{grid-template-columns:1fr}
    @media (min-width:960px){ .grid-2{grid-template-columns:1fr 1fr} }

    /* Forms */
    .row{display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end}
    label{font-size:12px; display:block; color:#475569}
    .field{display:flex; flex-direction:column; gap:6px; min-width:240px; flex:1}
    input,select{
      font:inherit; border-radius:10px; outline:none;
      border:1px solid var(--border); background:#fff; color:#111827;
      padding:10px 12px;
      transition:border-color .2s ease, box-shadow .2s ease;
    }
    input::placeholder,select:invalid{color:#9ca3af}
    input:focus,select:focus{
      border-color:var(--primary);
      box-shadow:0 0 0 3px var(--ring);
    }

    /* Alerts */
    .alert{padding:12px 14px; border-radius:10px; margin:14px 0;}
    .alert-success{background:var(--ok); color:var(--okc); border:1px solid var(--okb);}
    .alert-error{background:var(--err); color:var(--errc); border:1px solid var(--errb);}
    small.text-danger{color:var(--errc);}

    /* Table */
    .table-wrap{overflow:auto; border-radius:12px; border:1px solid var(--border);}
    table{width:100%; border-collapse:collapse;}
    thead th{
      background:var(--panel-2);
      border-bottom:1px solid var(--border);
      padding:12px;
      text-align:left; font-size:13px; color:#334155;
    }
    tbody td{border-bottom:1px solid var(--border); padding:12px; vertical-align:top;}
    tbody tr:hover td{background:var(--muted);}
    .actions form{display:inline;}

    /* Utilities */
    .muted{
      background:var(--panel-2);
      border:1px solid var(--border);
      border-radius:10px; padding:10px 12px; color:#475569;
    }
    .pill{
      display:inline-flex; align-items:center;
      padding:6px 12px; border-radius:999px;
      border:1px solid var(--border); font-size:13px;
      color:#1e293b; background:#f8fafc;
    }
    .mt-8{margin-top:8px} .mt-12{margin-top:12px} .ml-auto{margin-left:auto}
    .right{text-align:right}
    a{color:var(--primary-600); text-decoration:none} a:hover{text-decoration:underline}

    /* Auth layout */
    .auth-wrap{max-width:720px; margin:0 auto;}
    .headline{font-size:22px; font-weight:700;}
    .nrp-big{font-size:15px;}
    .stats-row{display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:10px;}
    .center{text-align:center}
  </style>
</head>
<body>
<div class="container">

  <div class="header">
    <div class="header-inner">
      <div class="brand">
        <div class="logo" aria-hidden="true"></div>
        <h1>Web-FRS</h1>
      </div>
      <div class="spacer"></div>
      @auth
      <div class="actions-right">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button class="btn" type="submit">Logout</button>
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
      <!-- Register -->
      <div class="section" @if($activePage !== 'register') style="display:none" @endif>
        <h2 class="center">Register</h2>
        <div class="sub center">Buat akun untuk mulai mengatur FRS</div>

        <form action="{{ route('register') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field"><label for="name">Nama</label><input id="name" name="name" type="text" placeholder="Nama lengkap" value="{{ old('name') }}"></div>
            <div class="field"><label for="email">Email</label><input id="email" name="email" type="text" placeholder="50242410xx@student.its.ac.id" value="{{ old('email') }}"></div>
            <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" placeholder="Minimal 8 karakter"></div>
            <button type="submit" class="btn ml-auto">Buat Akun</button>
          </div>
        </form>

        <div class="muted mt-12 center">Sudah punya akun? <a href="?page=login">Masuk di sini</a>.</div>
      </div>

      <!-- Login -->
      <div class="section" @if($activePage !== 'login') style="display:none" @endif>
        <h2 class="center">Login</h2>
        <div class="sub center">Masuk untuk melihat FRS dan waitlist</div>

        <form action="{{ route('login.submit') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field"><label for="loginname">Username / Email</label><input id="loginname" name="loginname" type="text" placeholder="nama / email" value="{{ old('loginname') }}"></div>
            <div class="field"><label for="loginpassword">Password</label><input id="loginpassword" name="loginpassword" type="password" placeholder="password"></div>
            <button type="submit" class="btn ml-auto">Masuk</button>
          </div>
        </form>

        <div class="muted mt-12 center">Belum punya akun? <a href="?page=register">Daftar di sini</a>.</div>
      </div>
    </div>
  @endguest

  @auth
    @php
      if (session()->has('sim_gpa')) { $gpa = session('sim_gpa'); }
      else { $gpa = round(mt_rand(100, 400) / 100, 2); session(['sim_gpa' => $gpa]); }
      if ($gpa < 2.50) $MAX_SKS = 18;
      elseif ($gpa < 3.00) $MAX_SKS = 20;
      elseif ($gpa < 3.50) $MAX_SKS = 22;
      else $MAX_SKS = 24;
    @endphp

    <div class="section">
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
          $label = $m->title.' ('.($m->sks ?? '-').' SKS) — '.$k->title.' · Kursi: '.$taken.'/'.($cap ?: '—');
          if ($isFull) $label .= ' (Penuh → waitlist)';
          if (!empty($k->start_time) && !empty($k->end_time)){
            $label .= ' · '.$k->start_time.'–'.$k->end_time.' ('.timeSlotName($k->start_time).')';
          }
          $pairs[] = (object)['value'=>$m->id.'|'.$k->id,'label'=>$label];
        }
      }
    @endphp

    <div class="grid grid-2">
      <div class="section">
        <h2>Ambil Matkul</h2>
        <div class="sub">Pilih kombinasi matkul + kelas yang tersedia</div>
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
                  @foreach($pairs as $p)<option value="{{ $p->value }}">{{ $p->label }}</option>@endforeach
                @endif
              </select>
            </div>
            <button type="submit" class="btn" {{ empty($pairs) ? 'disabled' : '' }}>Simpan</button>
          </div>
        </form>
      </div>

      <div class="section">
        <h2>Waitlist-ku</h2>
        @php
          $myWaits = \App\Models\Waitlist::with(['matkul','kelas'])
            ->where('user_id', auth()->id())->orderBy('created_at','asc')->get();
        @endphp
        <div class="table-wrap mt-12">
          <table>
            <thead><tr><th>#</th><th>Matkul</th><th>Kelas</th><th>Posisi</th><th>Keterangan</th><th>Di-antri</th><th>Aksi</th></tr></thead>
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
                <td>{{ $left>0 ? "$left kursi kosong (menunggu promosi)" : "Menunggu ada yang drop" }}</td>
                <td>{{ $w->created_at->format('Y-m-d H:i') }}</td>
                <td class="actions">
                  <form action="{{ route('waitlist.cancel',$w->id) }}" method="POST" onsubmit="return confirm('Batalkan waitlist?')">
                    @csrf @method('DELETE')
                    <button class="btn secondary" type="submit">Cancel</button>
                  </form>
                </td>
              </tr>
            @empty<tr><td colspan="7">Tidak ada antrian.</td></tr>@endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="section">
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
          <button class="btn" type="submit" {{ $totalSks > $MAX_SKS ? 'disabled' : '' }}>Submit FRS</button>
        </form>
      </div>

      <div class="table-wrap mt-12">
        <table>
          <thead><tr><th>#</th><th>Matkul</th><th>SKS</th><th>Kelas</th><th>Diambil</th><th>Aksi</th></tr></thead>
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
                  <div class="mt-8" style="color:#475569">Kapasitas:
                    {{ (\App\Models\Enrollment::where('kelas_id',$e->kelas->id)->count()) }} / {{ $e->kelas->capacity }}
                  </div>
                @endif
              </td>
              <td>{{ $e->created_at->format('Y-m-d H:i') }}</td>
              <td class="actions">
                <form action="{{ route('frs.drop', $e->id) }}" method="POST"
                      onsubmit="return confirm('Drop {{ $e->matkul->title }} — {{ $e->kelas->title }}?')">
                  @csrf @method('DELETE')
                  <button class="btn secondary" type="submit">Drop</button>
                </form>
              </td>
            </tr>
          @empty<tr><td colspan="6">Belum ada mata kuliah yang diambil.</td></tr>@endforelse
          <tr><td colspan="6" class="right"><strong>Total SKS: {{ $totalSks }}</strong></td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="grid grid-2">
      <div class="section">
        <h2>Matkul</h2>
        <form action="{{ route('matkul.create') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field">
              <label for="mkTitle">Nama Matkul</label>
              <input id="mkTitle" type="text" name="title" placeholder="Sistem Basis Data" required>
            </div>
            <button type="submit" class="btn ml-auto">Tambah</button>
          </div>
        </form>
        @error('title') <div class="alert alert-error mt-12">{{ $message }}</div> @enderror

        @php $matkulsAdmin = \App\Models\Matkul::orderBy('title')->get(); @endphp
        <div class="table-wrap mt-12">
          <table>
            <thead><tr><th>#</th><th>Nama Matkul</th><th>Aksi</th></tr></thead>
            <tbody>
              @forelse($matkulsAdmin as $i => $m)
                <tr>
                  <td>{{ $i+1 }}</td><td>{{ $m->title }}</td>
                  <td class="actions">
                    <form action="{{ route('matkul.destroy', $m->id) }}" method="POST"
                          onsubmit="return confirm('Hapus matkul {{ $m->title }}?')">
                      @csrf @method('DELETE')
                      <button class="btn secondary" type="submit">Delete</button>
                    </form>
                  </td>
                </tr>
              @empty<tr><td colspan="3">Belum ada data matkul.</td></tr>@endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="section">
        <h2>Kelas</h2>
        <form action="{{ route('kelas.create') }}" method="POST" class="mt-12">
          @csrf
          <div class="row">
            <div class="field">
              <label for="kelasTitle">Kode Kelas</label>
              <input id="kelasTitle" type="text" name="title" placeholder="IF 107" required>
            </div>
            <button type="submit" class="btn ml-auto">Tambah</button>
          </div>
        </form>
        @error('title') <div class="alert alert-error mt-12">{{ $message }}</div> @enderror

        @php $kelassAdmin = \App\Models\Kelas::orderBy('title')->get(); @endphp
        <div class="table-wrap mt-12">
          <table>
            <thead><tr><th>#</th><th>Kode Kelas</th><th>Aksi</th></tr></thead>
            <tbody>
              @forelse($kelassAdmin as $i => $k)
                <tr>
                  <td>{{ $i+1 }}</td><td>{{ $k->title }}</td>
                  <td class="actions">
                    <form action="{{ route('kelas.destroy', $k->id) }}" method="POST"
                          onsubmit="return confirm('Hapus kelas {{ $k->title }}?')">
                      @csrf @method('DELETE')
                      <button class="btn secondary" type="submit">Delete</button>
                    </form>
                  </td>
                </tr>
              @empty<tr><td colspan="3">Belum ada data kelas.</td></tr>@endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  @endauth

</div>
</body>
</html>
