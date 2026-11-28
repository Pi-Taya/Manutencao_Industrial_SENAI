@extends('layouts.app')

@section('title', 'Cadastrar usuário - PITAYA Control')

@section('content')
    <h1>Cadastrar Usuário</h1>

    @if($errors->any())
        <div class="alert alert-error">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-field">
                <label for="nome_usuario">Nome completo *</label>
                <input type="text" id="nome_usuario" name="nome_usuario" value="{{ old('nome_usuario') }}" required>
            </div>
            <div class="form-field">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-field">
                <label for="senha">Senha *</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="form-field">
                <label for="id_perfil">Perfil de acesso *</label>
                <select id="id_perfil" name="id_perfil" required>
                    <option value="">Selecione</option>
                    @foreach($perfis as $perfil)
                        <option value="{{ $perfil->id_perfil }}" {{ old('id_perfil') == $perfil->id_perfil ? 'selected' : '' }}>
                            {{ $perfil->tipo_perfil }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="especialidade">Especialidade</label>
                <select id="especialidade" name="especialidade">
                    @foreach(['Geral', 'Mecânica', 'Elétrica', 'Hidráulica', 'Pneumática', 'Automação'] as $especialidade)
                        <option value="{{ $especialidade }}" {{ old('especialidade', 'Geral') === $especialidade ? 'selected' : '' }}>{{ $especialidade }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="id_turno">Turno *</label>
                <select id="id_turno" name="id_turno" required>
                    <option value="">Selecione</option>
                    @foreach($turnos as $turno)
                        <option value="{{ $turno->id_turno }}" {{ old('id_turno') == $turno->id_turno ? 'selected' : '' }}>{{ $turno->nome_turno }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="id_setor">Setor principal *</label>
                <select id="id_setor" name="id_setor" required>
                    <option value="">Selecione</option>
                    @foreach($setores as $setor)
                        <option value="{{ $setor->id_setor }}" {{ old('id_setor') == $setor->id_setor ? 'selected' : '' }}>{{ $setor->nome_setor }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit">Cadastrar usuário</button>
            <a class="button button-secondary" href="{{ route('users.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
