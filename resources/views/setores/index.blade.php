@extends('layouts.app')

@section('title', 'Setores - PITAYA Control')

@section('content')
    <h1>Setores Fabris</h1>
    <p class="page-actions"><a class="button" href="{{ route('setores.create') }}">Cadastrar novo setor</a></p>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

    <div class="table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nome do setor</th><th>Descrição</th><th>Ações</th></tr></thead>
            <tbody>
                @forelse($setores as $setor)
                    <tr>
                        <td>{{ $setor->id_setor }}</td>
                        <td>{{ $setor->nome_setor }}</td>
                        <td>{{ $setor->descricao ?? 'Sem descrição' }}</td>
                        <td>
                            <a href="{{ route('setores.edit', $setor->id_setor) }}">Editar</a>
                            <form action="{{ route('setores.destroy', $setor->id_setor) }}" method="POST" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir este setor?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-link">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="4">Nenhum setor cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $setores->links() }}
@endsection
