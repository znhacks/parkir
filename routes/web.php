<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\ParkirController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Parkir routes for all users
    Route::resource('parkir', ParkirController::class);
    Route::post('parkir/{id}/keluar', [ParkirController::class, 'keluar'])->name('parkir.keluar');
    Route::get('riwayat', [ParkirController::class, 'history'])->name('parkir.history');
    Route::get('riwayat/download', [ParkirController::class, 'downloadHistory'])->name('parkir.download');

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::resource('kendaraan', KendaraanController::class);
    });
});
