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

    // Painel Geral: Acesso para Administrador, Gestor, PCM e Técnico (Operador = Não)
    Route::middleware(['perfil:Administrador,Gestor,PCM,Técnico'])->group(function () {
        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard');
    });

    // Rota provisória para o Operador enquanto o módulo de Chamados não é integrado
    Route::middleware(['perfil:Administrador,Gestor,PCM,Técnico,Operador'])->group(function () {
        Route::get('/chamados-aguardando', function () {
            return 'Área do Operador — Módulo de Chamados em desenvolvimento.';
        })->name('chamados.aguardando');
    });

    // Módulo Cadastro (Somente Administrador e Gestor)
    Route::middleware(['perfil:Gestor,Administrador'])->group(function () {
        Route::resource('setores', SetorController::class);
        Route::resource('fabricantes', FabricanteController::class);
        Route::resource('modelos', ModeloController::class);
        Route::resource('tipos-manutencao', TipoManutencaoController::class)->except(['show']);
        
        // Gestão de Usuários
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        // Cadastro, edição e exclusão de máquinas
        Route::resource('maquinas', MaquinaController::class)->except(['index', 'show']);
    });

    // Consulta de máquinas (Gestor, Administrador, PCM e Técnico)
    Route::middleware(['perfil:Técnico,PCM,Gestor,Administrador'])->group(function () {
        Route::resource('maquinas', MaquinaController::class)->only(['index', 'show']);
    });

    // Ordens de Serviço - Consulta e Abertura (Administrador, Gestor, PCM e Técnico; Operador = Não)
    Route::middleware(['perfil:Administrador,Gestor,PCM,Técnico'])->group(function () {
        Route::resource('ordens', OrdemServicoController::class)
            ->only(['index', 'create', 'store', 'show']);
    });

    // Ordens de Serviço - Execução Técnica e Parecer (Técnico e Gestor)
    Route::middleware(['perfil:Técnico,Gestor,Administrador'])->group(function () {
        Route::patch('ordens/{id}/status', [OrdemServicoController::class, 'atualizarStatus'])->name('ordens.status');
        Route::post('ordens/{id}/parecer', [OrdemServicoController::class, 'registrarParecer'])->name('ordens.parecer');
    });

    // Ordens de Serviço - Alocação e Exclusão (Gestor e Administrador)
    Route::middleware(['perfil:Gestor,Administrador'])->group(function () {
        Route::post('ordens/{id}/alocar-tecnicos', [OrdemServicoController::class, 'alocarTecnicos'])->name('ordens.alocar');
        Route::delete('ordens/{ordem}', [OrdemServicoController::class, 'destroy'])->name('ordens.destroy');
    });

});