@extends('layouts.app')

@section('title', 'Editar tipo de manutenção - PITAYA Control')

@section('content')
    <h1>Editar tipo de manutenção</h1>
    @if($errors->any())
        <div class="alert alert-error"><ul>@foreach($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('tipos-manutencao.update', $tipo->id_tipo_manutencao) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-field">
            <label for="nome_manutencao">Tipo de manutenção *</label>
            <input type="text" name="nome_manutencao" id="nome_manutencao" value="{{ old('nome_manutencao', $tipo->nome_manutencao) }}" maxlength="100" required>
        </div>
        <div class="form-field">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" maxlength="255">{{ old('descricao', $tipo->descricao) }}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit">Atualizar tipo de manutenção</button>
            <a class="button button-secondary" href="{{ route('tipos-manutencao.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
