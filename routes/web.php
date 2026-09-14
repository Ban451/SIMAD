<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SiswaRequestController;
use App\Http\Controllers\GuruRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============ GUEST ROUTES ============

// Landing Page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============ PROTECTED ROUTES ============
Route::middleware(['auth'])->group(function () {

    // Dashboard (redirect sesuai role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============ ROUTES UNTUK ADMIN ============
    Route::middleware(['role:admin'])->group(function () {
        // Dashboard Admin
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        
        // User Management
        Route::resource('users', UserController::class);
        
        // Kelas Management
        Route::resource('kelas', KelasController::class);
        
        // Dokumen Management (Full Access)
        Route::resource('dokumen', DokumenController::class)->except(['show']);
        Route::get('/dokumen/download/{id}', [DokumenController::class, 'download'])->name('dokumen.download');
        Route::get('/dokumen/preview/{id}', [DokumenController::class, 'preview'])->name('dokumen.preview');
    });

    // ============ ROUTES UNTUK GURU ============
    Route::middleware(['role:guru'])->group(function () {
        // Dashboard Guru
        Route::get('/guru/dashboard', [DashboardController::class, 'guruDashboard'])->name('guru.dashboard');
        
        // Dokumen Management (Untuk kelas yang diampu)
        Route::get('/guru/dokumen', [DokumenController::class, 'guruIndex'])->name('guru.dokumen.index');
        Route::get('/guru/dokumen/create', [DokumenController::class, 'guruCreate'])->name('guru.dokumen.create');
        Route::post('/guru/dokumen', [DokumenController::class, 'guruStore'])->name('guru.dokumen.store');
        Route::get('/guru/dokumen/{id}/edit', [DokumenController::class, 'guruEdit'])->name('guru.dokumen.edit');
        Route::put('/guru/dokumen/{id}', [DokumenController::class, 'guruUpdate'])->name('guru.dokumen.update');
        Route::delete('/guru/dokumen/{id}', [DokumenController::class, 'guruDestroy'])->name('guru.dokumen.destroy');
        Route::get('/guru/dokumen/download/{id}', [DokumenController::class, 'guruDownload'])->name('guru.dokumen.download');
        Route::get('/guru/dokumen/preview/{id}', [DokumenController::class, 'guruPreview'])->name('guru.dokumen.preview');
        
        // Data Siswa (Untuk kelas yang diampu)
        Route::get('/guru/siswa', [UserController::class, 'guruSiswa'])->name('guru.siswa');
        
        // ============ ROUTES UNTUK GURU MENANGANI REQUEST ============
        Route::post('/guru/request/{id}/process', [GuruRequestController::class, 'process'])->name('guru.request.process');
        Route::get('/guru/request/{id}/detail', [GuruRequestController::class, 'detail'])->name('guru.request.detail');
    });

    // ============ ROUTES UNTUK SISWA ============
    Route::middleware(['role:siswa'])->group(function () {
        // Dashboard Siswa
        Route::get('/siswa/dashboard', [DashboardController::class, 'siswaDashboard'])->name('siswa.dashboard');
        
        // Dokumen Saya (Read Only)
        Route::get('/dokumen-saya', [DokumenController::class, 'indexSiswa'])->name('dokumen.saya');
        Route::get('/dokumen-saya/download/{id}', [DokumenController::class, 'downloadSiswa'])->name('dokumen.saya.download');
        Route::get('/dokumen-saya/preview/{id}', [DokumenController::class, 'previewSiswa'])->name('dokumen.saya.preview');
        
        // Request Dokumen
        Route::post('/siswa/request', [SiswaRequestController::class, 'store'])->name('siswa.request.store');
        Route::get('/siswa/requests', [SiswaRequestController::class, 'index'])->name('siswa.requests');
    });
});