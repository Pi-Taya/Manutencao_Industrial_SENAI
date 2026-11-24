@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Peça: {{ $peca->codigo_item }}</h2>

    <form action="{{ route('pecas.update', $peca->id_peca) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Código Interno (SKU) *</label>
                <input type="text" name="codigo_item" class="form-control" value="{{ $peca->codigo_item }}" maxlength="50" required>
            </div>

            <div class="col-md-9">
                <label class="form-label">Descrição Padronizada *</label>
                <input type="text" name="nome_peca" class="form-control" value="{{ $peca->nome_peca }}" maxlength="100" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Unidade de Medida *</label>
                <select name="unidade_medida" class="form-select" required>
                    @foreach(['UN', 'KG', 'L', 'M', 'CX', 'PCT'] as $un)
                        <option value="{{ $un }}" {{ $peca->unidade_medida == $un ? 'selected' : '' }}>{{ $un }}</option>
                    @endforeach
                </select>
            </div>