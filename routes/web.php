<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrdemServicoController;
use App\Http\Controllers\TipoManutencaoController;
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

    // Cadastros e governança operacional: Gestor e Administrador.
    Route::middleware(['perfil:Gestor,Administrador'])->group(function () {
        Route::resource('setores', SetorController::class);
        Route::resource('fabricantes', FabricanteController::class);
        Route::resource('modelos', ModeloController::class);
        Route::resource('tipos-manutencao', TipoManutencaoController::class)->except(['show']);
        
        // Gestão de Usuários e Técnicos 
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        // Cadastro, edição e exclusão de máquinas
        Route::resource('maquinas', MaquinaController::class)->except(['index', 'show']);
    });

    // Consulta de máquinas para Gestor, Administrador e Técnico.
    Route::middleware(['perfil:Técnico,Gestor,Administrador'])->group(function () {
        Route::resource('maquinas', MaquinaController::class)->only(['index', 'show']);
    });

    // Consulta e abertura de Ordens: Administrador e Operador não atuam neste módulo.
    Route::middleware(['perfil:Técnico,Gestor,PCM'])->group(function () {
        Route::resource('ordens', OrdemServicoController::class)
            ->only(['index', 'create', 'store', 'show']);
    });

    // Execução de OS: PCM consulta e planeja, mas não altera execução nem parecer.
    Route::middleware(['perfil:Técnico,Gestor'])->group(function () {
        Route::patch('ordens/{id}/status', [OrdemServicoController::class, 'atualizarStatus'])->name('ordens.status');
        Route::post('ordens/{id}/parecer', [OrdemServicoController::class, 'registrarParecer'])->name('ordens.parecer');
    });

    Route::middleware(['perfil:Gestor'])->group(function () {
        Route::post('ordens/{id}/alocar-tecnicos', [OrdemServicoController::class, 'alocarTecnicos'])->name('ordens.alocar');
        Route::delete('ordens/{ordem}', [OrdemServicoController::class, 'destroy'])->name('ordens.destroy');
    });

});