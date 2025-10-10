<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KONTROLER;
use App\Http\Controllers\FRSController;

/*
|--------------------------------------------------------------------------
| Halaman Home (publik)
| - Guest: form register + login (sudah ada di Home.blade)
| - Auth : konten FRS
|--------------------------------------------------------------------------
*/
Route::view('/', 'Home')->name('home');

/*
|--------------------------------------------------------------------------
| Auth (publik)
|--------------------------------------------------------------------------
*/
// /login diarahkan ke home karena form login ada di Home.blade
Route::get('/login', fn () => redirect()->route('home'))->name('login');

// submit form
Route::post('/register', [KONTROLER::class, 'register'])->name('register');
Route::post('/login',    [KONTROLER::class, 'login'])->name('login.submit');

// logout hanya untuk user login
Route::post('/logout',   [KONTROLER::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Aksi FRS (wajib login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // master data (opsional dipakai sendiri)
    Route::post('/create-matkul', [FRSController::class, 'saveMatkul'])->name('matkul.create');
    Route::post('/create-kelas',  [FRSController::class, 'saveKelas'])->name('kelas.create');

    // enroll / drop / submit
    Route::post('/ambil-matkul',  [FRSController::class, 'enroll'])->name('frs.enroll');
    Route::delete('/frs/{enrollment}', [FRSController::class, 'drop'])->name('frs.drop');
    Route::post('/frs/submit', [FRSController::class, 'submit'])->name('frs.submit'); // << tambah ini
});
