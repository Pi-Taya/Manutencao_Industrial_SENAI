@extends('layouts.app')

@section('title', 'Editar modelo - PITAYA Control')

@section('content')
    <h1>Editar modelo</h1>
    @if($errors->any())
        <div class="alert alert-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('modelos.update', $modelo->id_modelo) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-field">
            <label for="nome_modelo">Nome do modelo *</label>
            <input type="text" id="nome_modelo" name="nome_modelo" value="{{ old('nome_modelo', $modelo->nome_modelo) }}" required>
        </div>
        <div class="form-field">
            <label for="id_fabricante">Fabricante *</label>
            <select id="id_fabricante" name="id_fabricante" required>
                <option value="">Selecione</option>
                @foreach($fabricantes as $fabricante)
                    <option value="{{ $fabricante->id_fabricante }}" {{ old('id_fabricante', $modelo->id_fabricante) == $fabricante->id_fabricante ? 'selected' : '' }}>{{ $fabricante->nome_fabricante }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-field">
            <label for="especificacoes_tecnicas">Especificações técnicas</label>
            <textarea id="especificacoes_tecnicas" name="especificacoes_tecnicas" rows="4">{{ old('especificacoes_tecnicas', $modelo->especificacoes_tecnicas) }}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit">Atualizar modelo</button>
            <a class="button button-secondary" href="{{ route('modelos.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
