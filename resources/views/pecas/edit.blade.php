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
            
            <div class="col-md-6">
                <label class="form-label">Estoque Atual *</label>
                <input type="number" name="quantidade_estoque" class="form-control" value="{{ $peca->quantidade_estoque }}" min="0" required>
            </div>

            <div class="col-12">
                <label class="form-label">Novo Registro de Status / Observação (Opcional)</label>
                <input type="text" name="observacao_status" class="form-control" placeholder="Ex: Reservado 1 UN para OS #105 ou Reposição recebida" maxlength="200">
            </div>

            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="status" id="status" {{ $peca->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Peça Ativa no Sistema</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Atualizar Peça</button>
                <a href="{{ route('pecas.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </form>