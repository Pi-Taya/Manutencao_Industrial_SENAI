@extends('layouts.app')

@section('title', 'Modelos - PITAYA Control')

@section('content')
    <h1>Modelos de Equipamentos</h1>
    <p class="page-actions"><a class="button" href="{{ route('modelos.create') }}">Cadastrar novo modelo</a></p>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

    <div class="table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nome do modelo</th><th>Fabricante</th><th>Especificações técnicas</th><th>Ações</th></tr></thead>
            <tbody>
                @forelse($modelos as $modelo)
                    <tr>
                        <td>{{ $modelo->id_modelo }}</td>
                        <td>{{ $modelo->nome_modelo }}</td>
                        <td>{{ $modelo->fabricante->nome_fabricante ?? 'N/A' }}</td>
                        <td>{{ $modelo->especificacoes_tecnicas ?? 'Sem especificações' }}</td>
                        <td>
                            <a href="{{ route('modelos.edit', $modelo->id_modelo) }}">Editar</a>
                            <form action="{{ route('modelos.destroy', $modelo->id_modelo) }}" method="POST" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir este modelo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-link">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="5">Nenhum modelo cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $modelos->links() }}
@endsection
