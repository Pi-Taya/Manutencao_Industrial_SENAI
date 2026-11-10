<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Rotas públicas de Autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas Protegidas por Autenticação
Route::middleware(['auth'])->group(function () {

    // Dashboard Principal (Página Inicial após Login)
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas de Administrador e Gestor
    Route::middleware(['perfil:Administrador,Gestor'])->group(function () {
        // Reservado para Gestão de Utilizadores, Pessoas e Relatórios Gerais
    });

    // Rotas de Técnicos, Gestores e Administradores
    Route::middleware(['perfil:Técnico,Gestor,Administrador'])->group(function () {
        // Reservado para Ordens de Serviço, Máquinas e Apontamentos
    });

});