<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\ModeloController;
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

    // Rotas de Gestão (Técnicos, Gestores e Administradores)
    Route::middleware(['perfil:Técnico,Gestor,Administrador'])->group(function () {
        
        // Módulo de Máquinas
        Route::get('/maquinas', [MaquinaController::class, 'index'])->name('maquinas.index');
        Route::get('/maquinas/criar', [MaquinaController::class, 'create'])->name('maquinas.create');
        Route::post('/maquinas', [MaquinaController::class, 'store'])->name('maquinas.store');

        // CRUDs Auxiliares (Setores, Fabricantes, Modelos)
        Route::resource('setores', SetorController::class);
        Route::resource('fabricantes', FabricanteController::class);
        Route::resource('modelos', ModeloController::class);
    });

});