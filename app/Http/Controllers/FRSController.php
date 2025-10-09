<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matkul;
use App\Models\Kelas;
use App\Models\Enrollment;

class FRSController extends Controller
{
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

    // matkul diambil (tabel)
    public function enroll(\Illuminate\Http\Request $r)
    {
        $data = $r->validate([
            'matkul_id' => 'required|exists:matkuls,id',
            'kelas_id'  => 'required|exists:kelas,id',
        ]);
    
        \App\Models\Enrollment::updateOrCreate(
            ['user_id' => auth()->id(), 'matkul_id' => $data['matkul_id']],
            ['kelas_id' => $data['kelas_id']]
        );
    
        return back()->with('ok','Pilihan FRS tersimpan!');
    }

    public function drop(\App\Models\Enrollment $enrollment)
{
    // mahasiswa aja yg bs drop
    abort_unless($enrollment->user_id === auth()->id(), 403);

    $enrollment->delete();

    return back()->with('ok', 'Mata kuliah di-drop.');
}

    
}
