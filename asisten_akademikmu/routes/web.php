<?php

use Illuminate\Support\Facades\Route;
// Import semua Controller sesuai dengan screenshot folder Anda
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\kalenderController;
use App\Http\Controllers\CatatanController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\pencapaianController;
use App\Http\Controllers\simulasiController;
use App\Http\Controllers\StudyTimerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Landing / Login
// Memberi nama 'login' sangat penting agar Laravel tidak bingung saat redirect
Route::get('/', function () {
    return view('index');
})->name('login'); 

// Proses Login (POST)
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth.manual'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mata Kuliah
    Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
    Route::post('/subjects/store', [MataKuliahController::class, 'store'])->name('subjects.store');
    Route::delete('/subjects/{id}', [MataKuliahController::class, 'destroy']);
    // Kalender
    Route::get('/kalender', [kalenderController::class, 'index'])->name('kalender');
    // HAPUS atau KOMENTAR baris TaskController jika tidak ada filenya
    // Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');

    // Study Timer
    Route::get('/study-timer', [StudyTimerController::class, 'index'])->name('study-timer');

    // CHECKLIST - INI SUDAH BENAR
    Route::get('/checklist', [ChecklistController::class, 'index'])->name('checklist.index');
    Route::post('/checklist/store', [ChecklistController::class, 'store'])->name('checklist.store');
    Route::post('/checklist/toggle', [ChecklistController::class, 'toggle'])->name('checklist.toggle');

    // Catatan
   
    Route::get('/catatan', [CatatanController::class, 'index'])->name('catatan');
    Route::post('/catatan/store', [CatatanController::class, 'store'])->name('catatan.store');
    Route::delete('/catatan/delete/{id}', [CatatanController::class, 'destroy'])->name('catatan.delete');

    // Pencapaian
    Route::get('/pencapaian', [pencapaianController::class, 'index'])->name('pencapaian');

    // Simulasi
    Route::get('/simulasi', [simulasiController::class, 'index'])->name('simulasi');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});