<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PontoController;
use App\Http\Controllers\HistoricoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HoraExtraController; // Add this line
use App\Http\Controllers\RelatorioController; // Add this line

// Página inicial (dashboard) - sem autenticação necessária
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rotas públicas
Route::post('/ponto/register', [PontoController::class, 'register']);
Route::get('/ponto/status', [PontoController::class, 'status']);

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
    Route::get('/historico', [HistoricoController::class, 'index'])->name('historico');
    Route::get('/historico/dados', [HistoricoController::class, 'getData']);
});

// Admin routes
Route::middleware(['auth'])->group(function () {
    // Rotas do admin
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/historico', [HistoricoController::class, 'index'])->name('admin.historico');
    Route::get('/admin/hora-extra', [HoraExtraController::class, 'overtime'])->name('admin.hora-extra'); // Updated route
    Route::get('/admin/hora-atraso', [AdminController::class, 'lateHours'])->name('admin.hora-atraso');
    Route::get('/admin/relatorio', [RelatorioController::class, 'index'])->name('admin.relatorio'); // Updated route
    Route::get('/admin/relatorio/dados', [RelatorioController::class, 'relatorioData'])->name('admin.relatorio.dados'); // New route

    // Rotas de usuários
    Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');

    Route::put('/admin/working-hours', [AdminController::class, 'updateWorkingHours'])->name('admin.working-hours.update');
});



