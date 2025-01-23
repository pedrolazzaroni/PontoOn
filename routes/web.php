<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Rota protegida pelo middleware 'auth'
Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Atualizar rotas de autenticação
Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('auth');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
