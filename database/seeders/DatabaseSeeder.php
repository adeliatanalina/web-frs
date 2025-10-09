<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matkul;
use App\Models\Kelas;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Matkul::upsert([
            ['title' => 'Sistem Basis Data'],
            ['title' => 'Jaringan Komputer'],
            ['title' => 'Pemrograman Web'],
            ['title' => 'Kalkulus I'],
            ['title' => 'Kalkulus II'],
            ['title' => 'Kalkulus III'],
            ['title' => 'Dasar Pemrograman'],
        ], ['title']);

        Kelas::upsert([
            ['title' => 'IF 107'],
            ['title' => 'IF 108'],
            ['title' => 'IF 109'],
            ['title' => 'IF 110'],
            ['title' => 'IF 111'],
            ['title' => 'IF 112'],
        ], ['title']);
    }
}
