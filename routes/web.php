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
        // Ordens de Serviço 
        Route::resource('ordens', OrdemServicoController::class);
        Route::post('ordens/{id}/alocar-tecnicos', [OrdemServicoController::class, 'alocarTecnicos'])->name('ordens.alocar');
        Route::patch('ordens/{id}/status', [OrdemServicoController::class, 'atualizarStatus'])->name('ordens.status');
    });

    // Rotas de Estrutura Fabril e Gestão de Contas (Apenas Gestores e Administradores)
    Route::middleware(['perfil:Gestor,Administrador'])->group(function () {
        Route::resource('maquinas', MaquinaController::class);
        Route::resource('setores', SetorController::class);
        Route::resource('fabricantes', FabricanteController::class);
        Route::resource('modelos', ModeloController::class);
        
        // Gestão de Usuários e Técnicos 
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    });

});