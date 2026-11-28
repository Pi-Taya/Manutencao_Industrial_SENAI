@extends('layouts.app')

@section('title', 'Editar setor - PITAYA Control')

@section('content')
    <h1>Editar setor</h1>
    @if($errors->any())
        <div class="alert alert-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('setores.update', $setor->id_setor) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-field">
            <label for="nome_setor">Nome do setor *</label>
            <input type="text" id="nome_setor" name="nome_setor" value="{{ old('nome_setor', $setor->nome_setor) }}" required>
        </div>
        <div class="form-field">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="3">{{ old('descricao', $setor->descricao) }}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit">Atualizar setor</button>
            <a class="button button-secondary" href="{{ route('setores.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
