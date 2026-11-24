@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Controle de Peças e Almoxarifado</h2>
        <a href="{{ route('pecas.create') }}" class="btn btn-primary">+ Nova Peça</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Cód. (SKU)</th>
                <th>Descrição Padronizada</th>
                <th>Estoque Atual</th>
                <th>Ficha / Status do Estoque</th>
                <th>Situação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>