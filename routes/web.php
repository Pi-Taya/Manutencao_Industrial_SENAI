<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrdemServicoController;
use Illuminate\Support\Facades\Route;

// Rotas públicas de Autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas Protegidas por Autenticação
Route::middleware(['auth'])->group(function () {

    // Dashboard Principal
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas de Gestão Operacional (Técnicos, Gestores e Administradores)
    Route::middleware(['perfil:Técnico,Gestor,Administrador'])->group(function () {
        // Máquinas (Leitura, Cadastro e Operação para chão de fábrica)
        Route::resource('maquinas', MaquinaController::class);

        // Ordens de Serviço (RF07 / RF08)
        Route::resource('ordens', OrdemServicoController::class);
    });

    // Rotas de Estrutura Fabril e Gestão de Contas (Apenas Gestores e Administradores)
    Route::middleware(['perfil:Gestor,Administrador'])->group(function () {
        Route::resource('setores', SetorController::class);
        Route::resource('fabricantes', FabricanteController::class);
        Route::resource('modelos', ModeloController::class);
        
        // Gestão de Usuários e Técnicos 
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    });

});