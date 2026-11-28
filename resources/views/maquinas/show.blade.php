@extends('layouts.app')

@section('title', 'Detalhes da máquina - PITAYA Control')

@section('content')
    <h1>Detalhes da Máquina: {{ $maquina->nome_maquina }}</h1>
    <a href="{{ route('maquinas.index') }}">Voltar à Lista</a>
    @if(in_array(auth()->user()->perfil->tipo_perfil ?? '', ['Gestor', 'Administrador']))
        | <a href="{{ route('maquinas.edit', $maquina->id_maquina) }}">Editar</a>
    @endif

    <div class="table-wrap">
    <table>
        <tr>
            <th>Código TAG:</th>
            <td>{{ $maquina->codigo_tag }}</td>
        </tr>
        <tr>
            <th>Nome da Máquina:</th>
            <td>{{ $maquina->nome_maquina }}</td>
        </tr>
        <tr>
            <th>Modelo:</th>
            <td>{{ $maquina->modelo->nome_modelo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Fabricante:</th>
            <td>{{ $maquina->fabricante->nome_fabricante ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Setor:</th>
            <td>{{ $maquina->setor->nome_setor ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Data de Aquisição:</th>
            <td>{{ $maquina->data_aquisicao?->format('d/m/Y') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Status:</th>
            <td>{{ $maquina->statusMaquina->estado_da_maquina ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Criticidade:</th>
            <td>
                {{ $maquina->criticidade }} —
                {{ [1 => 'Muito Baixa', 2 => 'Baixa', 3 => 'Moderada', 4 => 'Alta', 5 => 'Crítica'][$maquina->criticidade] ?? 'N/A' }}
            </td>
        </tr>
    </table>
    </div>
@endsection