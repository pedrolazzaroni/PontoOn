<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PontoController;
use App\Http\Controllers\HistoricoController;
use App\Http\Controllers\AdminController;

// Public Dashboard Route
Route::get('/', [DashboardController::class, 'index']);

// Guest routes (login/register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showAuthForm'])->name('login');  // Corrigido de '/post' para '/login'
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
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

// Admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.users.toggle-status');
});

// Authentication Routes...
// Remove the default Auth::routes() as custom routes are defined above
// Auth::routes();



