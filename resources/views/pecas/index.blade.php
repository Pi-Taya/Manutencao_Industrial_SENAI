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
        @foreach($pecas as $peca)
            <tr>
                <td><strong>{{ $peca->codigo_item }}</strong></td>
                <td>{{ $peca->nome_peca }}</td>
                <td><strong>{{ $peca->quantidade_estoque }} {{ $peca->unidade_medida }}</strong></td>
                <td>{{ $peca->ultimoStatus->descricao_status_peca ?? 'Sem registro' }}</td>
                <td>
                    @if($peca->status)
                        <span class="badge bg-success">Ativa</span>
                    @else
                        <span class="badge bg-secondary">Inativa</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('pecas.edit', $peca->id_peca) }}" class="btn btn-sm btn-warning">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection