@extends('layouts.app')

@section('title', 'Editar fabricante - PITAYA Control')

@section('content')
    <h1>Editar fabricante</h1>
    @if($errors->any())
        <div class="alert alert-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('fabricantes.update', $fabricante->id_fabricante) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-field">
            <label for="nome_fabricante">Nome do fabricante *</label>
            <input type="text" id="nome_fabricante" name="nome_fabricante" value="{{ old('nome_fabricante', $fabricante->nome_fabricante) }}" required>
        </div>
        <div class="form-field">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $fabricante->telefone) }}">
        </div>
        <div class="form-field">
            <label for="email">E-mail de contato</label>
            <input type="email" id="email" name="email" value="{{ old('email', $fabricante->email) }}">
        </div>
        <div class="form-actions">
            <button type="submit">Atualizar fabricante</button>
            <a class="button button-secondary" href="{{ route('fabricantes.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
