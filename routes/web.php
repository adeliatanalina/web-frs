<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KONTROLER;
use App\Http\Controllers\FRSController;

Route::view('/', 'Home')->name('home');

// auth
// GET /login dipake middleware('auth') buat redirect
Route::get('/login', fn () => redirect()->route('home'))->name('login');

// form actions
Route::post('/register', [KONTROLER::class, 'register'])->name('register');
Route::post('/login',    [KONTROLER::class, 'login'])->name('login.submit');
Route::post('/logout',   [KONTROLER::class, 'logout'])->middleware('auth')->name('logout');

// protected (harus login)
Route::middleware('auth')->group(function () {
    // master data buat edit matkul/kelasnya
    Route::post('/create-matkul', [FRSController::class, 'saveMatkul'])->name('matkul.create');
    Route::post('/create-kelas',  [FRSController::class, 'saveKelas'])->name('kelas.create');

    // enroll FRS
    Route::post('/ambil-matkul',  [FRSController::class, 'enroll'])->name('frs.enroll');

    // drop FRS
    Route::delete('/frs/{enrollment}', [FRSController::class, 'drop'])->name('frs.drop');
});
