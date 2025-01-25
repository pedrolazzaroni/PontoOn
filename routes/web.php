<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PontoController;
use App\Http\Controllers\HistoricoController;

// Dashboard route with auth middleware
Route::get('/', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Guest routes (login/register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showAuthForm'])->name('login'); // Alterado de /auth para /login
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Auth routes (logout)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Ponto routes
Route::middleware(['auth'])->group(function () {
    Route::post('/ponto/register', [PontoController::class, 'register'])->name('ponto.register');
    Route::get('/ponto/status', [PontoController::class, 'status'])->name('ponto.status');
    Route::post('/ponto/consultar', [PontoController::class, 'consultar'])->name('ponto.consultar');
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/historico', [HistoricoController::class, 'index'])->name('historico.index');
    Route::get('/historico/dados', [HistoricoController::class, 'getData'])->name('historico.dados');
});


