<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PontoController;

// Dashboard route with auth middleware
Route::get('/', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Guest routes (login/register)
Route::middleware('guest')->group(function () {
    Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('auth');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Auth routes (logout)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Ponto routes
Route::middleware('auth')->group(function () {
    Route::post('/ponto/register', [PontoController::class, 'register'])->name('ponto.register');
    Route::get('/ponto/status', [PontoController::class, 'status'])->name('ponto.status');
});
