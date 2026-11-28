@extends('layouts.app')

@section('title', 'Editar usuário - PITAYA Control')

@section('content')
    <h1>Editar Usuário</h1>

    @if($errors->any())
        <div class="alert alert-error">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('users.update', $usuario->id_usuario) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-field">
                <label for="nome_usuario">Nome completo *</label>
                <input type="text" id="nome_usuario" name="nome_usuario" value="{{ old('nome_usuario', $usuario->nome_usuario) }}" required>
            </div>
            <div class="form-field">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
            </div>
            <div class="form-field">
                <label for="senha">Nova senha (opcional)</label>
                <input type="password" id="senha" name="senha">
            </div>
            <div class="form-field">
                <label for="id_perfil">Perfil de acesso *</label>
                <select id="id_perfil" name="id_perfil" required>
                    @foreach($perfis as $perfil)
                        <option value="{{ $perfil->id_perfil }}" {{ old('id_perfil', $usuario->id_perfil) == $perfil->id_perfil ? 'selected' : '' }}>{{ $perfil->tipo_perfil }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="especialidade">Especialidade</label>
                <select id="especialidade" name="especialidade">
                    @foreach(['Geral', 'Mecânica', 'Elétrica', 'Hidráulica', 'Pneumática', 'Automação'] as $especialidade)
                        <option value="{{ $especialidade }}" {{ old('especialidade', $usuario->especialidade) == $especialidade ? 'selected' : '' }}>{{ $especialidade }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="id_turno">Turno *</label>
                <select id="id_turno" name="id_turno" required>
                    @foreach($turnos as $turno)
                        <option value="{{ $turno->id_turno }}" {{ old('id_turno', $usuario->id_turno) == $turno->id_turno ? 'selected' : '' }}>{{ $turno->nome_turno }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="id_setor">Setor principal *</label>
                <select id="id_setor" name="id_setor" required>
                    @foreach($setores as $setor)
                        <option value="{{ $setor->id_setor }}" {{ old('id_setor', $usuario->id_setor) == $setor->id_setor ? 'selected' : '' }}>{{ $setor->nome_setor }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    <option value="1" {{ old('status', $usuario->status) == 1 ? 'selected' : '' }}>Ativo</option>
                    <option value="0" {{ old('status', $usuario->status) == 0 ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit">Atualizar usuário</button>
            <a class="button button-secondary" href="{{ route('users.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
