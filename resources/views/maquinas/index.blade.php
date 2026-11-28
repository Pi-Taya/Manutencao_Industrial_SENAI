@extends('layouts.app')

@section('title', 'Máquinas - PITAYA Control')

@section('content')
    <h1>Máquinas Cadastradas</h1>

    <p>
        @if(in_array(auth()->user()->perfil->tipo_perfil ?? '', ['Gestor', 'Administrador']))
            <a href="{{ route('maquinas.create') }}">+ Cadastrar Nova Máquina</a> | 
        @endif
        <a href="{{ route('dashboard') }}">Voltar ao Dashboard</a>
    </p>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Código (TAG)</th>
                <th>Nome</th>
                <th>Modelo</th>
                <th>Setor</th>
                <th>Fabricante</th>
                <th>Data de Aquisição</th>
                <th>Criticidade</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maquinas as $maquina)
                <tr>
                    <td>{{ $maquina->codigo_tag }}</td>
                    <td>{{ $maquina->nome_maquina }}</td>
                    <td>{{ $maquina->modelo->nome_modelo ?? 'N/A' }}</td>
                    <td>{{ $maquina->setor->nome_setor ?? 'N/A' }}</td>
                    <td>{{ $maquina->fabricante->nome_fabricante ?? 'N/A' }}</td>
                    <td>{{ $maquina->data_aquisicao?->format('d/m/Y') ?? 'N/A' }}</td>
                    <td>
                        {{ $maquina->criticidade }} —
                        {{ [1 => 'Muito Baixa', 2 => 'Baixa', 3 => 'Moderada', 4 => 'Alta', 5 => 'Crítica'][$maquina->criticidade] ?? 'N/A' }}
                    </td>
                    <td>{{ $maquina->statusMaquina->estado_da_maquina ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('maquinas.show', $maquina->id_maquina) }}">Visualizar</a>

                        @if(in_array(auth()->user()->perfil->tipo_perfil ?? '', ['Gestor', 'Administrador']))
                            | <a href="{{ route('maquinas.edit', $maquina->id_maquina) }}">Editar</a> | 
                            <form action="{{ route('maquinas.destroy', $maquina->id_maquina) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta máquina?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-link">Excluir</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Nenhuma máquina cadastrada até o momento.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div style="margin-top: 15px;">
        {{ $maquinas->links() }}
    </div>
@endsection