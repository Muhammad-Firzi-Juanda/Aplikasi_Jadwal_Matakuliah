<?php

use App\Enums\UserRole;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\FakultasDashboardController;
use App\Http\Controllers\JadwalKuliahController;
use App\Http\Controllers\JurusanDashboardController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\PenjadwalanController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProdiDashboardController;
use App\Http\Controllers\ProdiManagementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root route redirects to dashboard or role landing
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            UserRole::ADMIN => redirect()->route('manajemen-akun'),
            UserRole::FAKULTAS => redirect()->route('fakultas.dashboard'),
            UserRole::JURUSAN => redirect()->route('jurusan.dashboard'),
            UserRole::PRODI => redirect()->route('prodi.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    return redirect()->route('dashboard');
});

// Guest routes (Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1')->name('login.submit');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

    // User Management (CRUD akun) routes for Super Admin & Admin
    Route::middleware('check_role:Super Admin,Admin')->group(function () {
        Route::get('/manajemen-akun', [UserController::class, 'manajemenAkun'])->name('manajemen-akun');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Super Admin exclusive routes
    Route::middleware('super_admin')->group(function () {
        Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

        Route::post('/fakultas', [FakultasController::class, 'store'])->name('fakultas.store');
        Route::put('/fakultas/{fakultas}', [FakultasController::class, 'update'])->name('fakultas.update');
        Route::delete('/fakultas/{fakultas}', [FakultasController::class, 'destroy'])->name('fakultas.destroy');

        Route::post('/mata-kuliah', [MataKuliahController::class, 'store'])->name('mata-kuliah.store');
        Route::put('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'update'])->name('mata-kuliah.update');
        Route::delete('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('mata-kuliah.destroy');
    });

    // Super Admin & Fakultas routes for Scheduling & Prodi management
    Route::middleware('check_role:Super Admin,Fakultas')->group(function () {
        Route::post('/prodi', [ProdiController::class, 'store'])->name('prodi.store');
        Route::put('/prodi/{prodi}', [ProdiController::class, 'update'])->name('prodi.update');
        Route::delete('/prodi/{prodi}', [ProdiController::class, 'destroy'])->name('prodi.destroy');
        Route::get('/jadwal-kuliah', [JadwalKuliahController::class, 'index'])->name('jadwal-kuliah.index');

        Route::get('/penjadwalan', [PenjadwalanController::class, 'index'])->name('penjadwalan.index');
        Route::post('/penjadwalan/mata-kuliah', [PenjadwalanController::class, 'store'])->name('penjadwalan.store');
        Route::put('/penjadwalan/mata-kuliah/{mataKuliah}', [PenjadwalanController::class, 'update'])->name('penjadwalan.update');
        Route::delete('/penjadwalan/mata-kuliah/{mataKuliah}', [PenjadwalanController::class, 'destroy'])->name('penjadwalan.destroy');
        Route::post('/penjadwalan/generate', [PenjadwalanController::class, 'generate'])->name('penjadwalan.generate');
        Route::get('/penjadwalan/template', [PenjadwalanController::class, 'downloadTemplate'])->name('penjadwalan.template');
        Route::post('/penjadwalan/import', [PenjadwalanController::class, 'import'])->name('penjadwalan.import');
    });

    // Admin Fakultas routes
    Route::middleware('check_role:Fakultas')->group(function () {
        Route::get('/fakultas/dashboard', [FakultasDashboardController::class, 'index'])->name('fakultas.dashboard');
    });

    // Admin Jurusan routes
    Route::middleware('check_role:Jurusan')->group(function () {
        Route::get('/jurusan/dashboard', [JurusanDashboardController::class, 'index'])->name('jurusan.dashboard');
    });

    // Admin Prodi routes
    Route::middleware('check_role:Prodi')->group(function () {
        Route::get('/prodi/dashboard', [ProdiDashboardController::class, 'index'])->name('prodi.dashboard');
        Route::post('/prodi/jadwal', [ProdiManagementController::class, 'storeJadwal'])->name('prodi.jadwal.store');
    });
});
