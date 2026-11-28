@extends('layouts.app')

@section('title', 'Tipos de manutenção - PITAYA Control')

@section('content')
    <h1>Tipos de Manutenção</h1>
    <p class="page-actions"><a class="button" href="{{ route('tipos-manutencao.create') }}">Novo tipo de manutenção</a></p>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

    <div class="table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Tipo de manutenção</th><th>Descrição</th><th>Ações</th></tr></thead>
            <tbody>
                @forelse($tipos as $tipo)
                    <tr>
                        <td>{{ $tipo->id_tipo_manutencao }}</td>
                        <td>{{ $tipo->nome_manutencao }}</td>
                        <td>{{ $tipo->descricao ?? 'Sem descrição' }}</td>
                        <td>
                            <a href="{{ route('tipos-manutencao.edit', $tipo->id_tipo_manutencao) }}">Editar</a>
                            <form action="{{ route('tipos-manutencao.destroy', $tipo->id_tipo_manutencao) }}" method="POST" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir este tipo de manutenção?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-link">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="4">Nenhum tipo de manutenção cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
