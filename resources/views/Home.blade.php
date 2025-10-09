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
    th,td{border:1px solid #ddd;padding:8px;text-align:left}
    thead th{background:var(--muted)}
    hr{margin:28px 0}
    .actions form{display:inline}
  </style>
</head>
<body>

  
  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  

  @guest
    <h2>Web-FRS</h2>

    <div class="box">
      <h3>Register</h3>
      <form action="{{ route('register') }}" method="POST" class="row">
        @csrf
        <input name="name" type="text" placeholder="name" value="{{ old('name') }}">
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror

        <input name="email" type="text" placeholder="email" value="{{ old('email') }}">
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror

        <input name="password" type="password" placeholder="password">
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror

        <button type="submit">Register</button>
      </form>
    </div>

    <div class="box">
      <h3>Login</h3>
      <form action="{{ route('login') }}" method="POST" class="row">
        @csrf
        <input name="loginname" type="text" placeholder="name" value="{{ old('loginname') }}">
        @error('loginname') <small class="text-danger">{{ $message }}</small> @enderror

        <input name="loginpassword" type="password" placeholder="password">
        @error('loginpassword') <small class="text-danger">{{ $message }}</small> @enderror

        <button type="submit">Login</button>
      </form>
    </div>
  @endguest



  @auth
    <p>masuk dia njir</p>

    <form action="{{ route('logout') }}" method="POST" class="box">
      @csrf
      <button type="submit">log out</button>
    </form>


    
{{-- 
    <h2>Matkul</h2>
    <div class="box">
      <form action="{{ route('matkul.create') }}" method="POST" class="row">
        @csrf
        <input type="text" name="title" placeholder="contoh: Sistem Basis Data" required>
        <button type="submit">Tambah Matkul</button>
      </form>
      @error('title') <div class="alert alert-error">{{ $message }}</div> @enderror

      @php $matkuls = \App\Models\Matkul::orderBy('title')->get(); @endphp
      <table style="margin-top:10px">
        <thead>
          <tr>
            <th style="width:60px">#</th>
            <th>Nama Matkul</th>
          </tr>
        </thead>
        <tbody>
          @forelse($matkuls as $i => $m)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $m->title }}</td>
            </tr>
          @empty
            <tr><td colspan="2">Belum ada data matkul.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>



    <h2>Kelas</h2>
    <div class="box">
      <form action="{{ route('kelas.create') }}" method="POST" class="row">
        @csrf
        <input type="text" name="title" placeholder="contoh: IF 107" required>
        <button type="submit">Tambah Kelas</button>
      </form>
      @error('title') <div class="alert alert-error">{{ $message }}</div> @enderror

      @php $kelass = \App\Models\Kelas::orderBy('title')->get(); @endphp
      <table style="margin-top:10px">
        <thead>
          <tr>
            <th style="width:60px">#</th>
            <th>Kode Kelas</th>
          </tr>
        </thead>
        <tbody>
          @forelse($kelass as $i => $k)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $k->title }}</td>
            </tr>
          @empty
            <tr><td colspan="2">Belum ada data kelas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <hr />
--}}
    

    @php
      $matkuls = \App\Models\Matkul::orderBy('title')->get();
      $kelass  = \App\Models\Kelas::orderBy('title')->get();
    @endphp

    <h2>Ambil Matkul</h2>
    <div class="box">
      <form action="{{ route('frs.enroll') }}" method="POST" class="row">
        @csrf


        <label>Matkul</label>
        <select name="matkul_id" required {{ $matkuls->isEmpty() ? 'disabled' : '' }}>
          @if($matkuls->isEmpty())
            <option>Belum ada matkul — tambah dulu di atas</option>
          @else
            <option value="" disabled selected>-- Pilih Matkul --</option>
            @foreach($matkuls as $m)
              <option value="{{ $m->id }}">{{ $m->title }}</option>
            @endforeach
          @endif
        </select>
        @error('matkul_id') <small class="text-danger">{{ $message }}</small> @enderror

        <label>Kelas</label>
        <select name="kelas_id" required {{ $kelass->isEmpty() ? 'disabled' : '' }}>
          @if($kelass->isEmpty())
            <option>Belum ada kelas — tambah dulu di atas</option>
          @else
            <option value="" disabled selected>-- Pilih Kelas --</option>
            @foreach($kelass as $k)
              <option value="{{ $k->id }}">{{ $k->title }}</option>
            @endforeach
          @endif
        </select>
        @error('kelas_id') <small class="text-danger">{{ $message }}</small> @enderror

        <button type="submit" {{ ($matkuls->isEmpty() || $kelass->isEmpty()) ? 'disabled' : '' }}>
          Simpan Pilihan
        </button>
      </form>

      @if(session('ok'))
        <div class="alert alert-success" style="margin-top:10px">{{ session('ok') }}</div>
      @endif
    </div>


    


    <div class="box">
      <h3>FRS-ku</h3>
      @php
        $enrolls = \App\Models\Enrollment::with(['matkul','kelas'])
                   ->where('user_id', auth()->id())->get();
      @endphp
      <table>
        <thead>
          <tr>
            <th style="width:60px">#</th>
            <th>Matkul</th>
            <th>Kelas</th>
            <th style="width:180px">Diambil Pada</th>
            <th style="width:120px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($enrolls as $i => $e)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $e->matkul->title }}</td>
              <td>{{ $e->kelas->title }}</td>
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
            <tr><td colspan="5">Belum ada mata kuliah yang diambil.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @endauth

</body>
</html>
