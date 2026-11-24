@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Cadastrar Nova Peça</h2>

    <form action="{{ route('pecas.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Código Interno (SKU) *</label>
                <input type="text" name="codigo_item" class="form-control" placeholder="Ex: 10452" maxlength="50" required>
            </div>

            <div class="col-md-9">
                <label class="form-label">Descrição Padronizada *</label>
                <input type="text" name="nome_peca" class="form-control" placeholder="Ex: ROLAMENTO 6309-2Z SKF" maxlength="100" required>
            </div>