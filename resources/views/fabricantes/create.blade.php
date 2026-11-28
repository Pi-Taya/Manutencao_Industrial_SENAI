@extends('layouts.app')

@section('title', 'Cadastrar fabricante - PITAYA Control')

@section('content')
    <h1>Cadastrar novo fabricante</h1>
    @if($errors->any())
        <div class="alert alert-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('fabricantes.store') }}" method="POST" class="card">
        @csrf
        <div class="form-field">
            <label for="nome_fabricante">Nome do fabricante *</label>
            <input type="text" id="nome_fabricante" name="nome_fabricante" value="{{ old('nome_fabricante') }}" required>
        </div>
        <div class="form-field">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}">
        </div>
        <div class="form-field">
            <label for="email">E-mail de contato</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="form-actions">
            <button type="submit">Salvar fabricante</button>
            <a class="button button-secondary" href="{{ route('fabricantes.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
