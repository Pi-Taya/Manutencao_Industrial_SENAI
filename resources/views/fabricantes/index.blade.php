@extends('layouts.app')

@section('title', 'Fabricantes - PITAYA Control')

@section('content')
    <h1>Fabricantes de Equipamentos</h1>
    <p class="page-actions"><a class="button" href="{{ route('fabricantes.create') }}">Cadastrar novo fabricante</a></p>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

    <div class="table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nome</th><th>Telefone</th><th>E-mail</th><th>Ações</th></tr></thead>
            <tbody>
                @forelse($fabricantes as $fabricante)
                    <tr>
                        <td>{{ $fabricante->id_fabricante }}</td>
                        <td>{{ $fabricante->nome_fabricante }}</td>
                        <td>{{ $fabricante->telefone ?? 'N/A' }}</td>
                        <td>{{ $fabricante->email ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('fabricantes.edit', $fabricante->id_fabricante) }}">Editar</a>
                            <form action="{{ route('fabricantes.destroy', $fabricante->id_fabricante) }}" method="POST" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir este fabricante?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-link">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="5">Nenhum fabricante cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $fabricantes->links() }}
@endsection
