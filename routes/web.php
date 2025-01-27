<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PontoController;
use App\Http\Controllers\HistoricoController;

// Public Dashboard Route
Route::get('/', [DashboardController::class, 'index']);

// Guest routes (login/register)
Route::middleware('guest')->group(function () {
    Route::get('/post', [AuthController::class, 'showAuthForm'])->name('login'); // Alterado de /auth para /login
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Auth routes (logout)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protect other routes with auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/ponto/register', [PontoController::class, 'register']);
    Route::get('/ponto/status', [PontoController::class, 'status']);
    Route::post('/ponto/consultar', [PontoController::class, 'consultar'])->name('ponto.consultar');
    Route::get('/historico', [HistoricoController::class, 'index'])->name('historico.index');
    Route::get('/historico/dados', [HistoricoController::class, 'getData']);
});

// Authentication Routes...
// Remove the default Auth::routes() as custom routes are defined above
// Auth::routes();



