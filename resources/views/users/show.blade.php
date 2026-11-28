@extends('layouts.app')

@section('title', 'Detalhes do usuário - PITAYA Control')

@section('content')
    <h1>Detalhes do Usuário</h1>
    <p><a class="button button-secondary" href="{{ route('users.index') }}">Voltar à lista</a></p>

    <section class="card">
        <dl class="details-list">
            <dt>Nome</dt><dd>{{ $usuario->nome_usuario }}</dd>
            <dt>E-mail</dt><dd>{{ $usuario->email }}</dd>
            <dt>Perfil de acesso</dt><dd>{{ $usuario->perfil->tipo_perfil ?? 'N/A' }}</dd>
            <dt>Especialidade</dt><dd>{{ $usuario->especialidade ?? 'N/A' }}</dd>
            <dt>Turno de trabalho</dt><dd>{{ $usuario->turno->nome_turno ?? 'N/A' }}</dd>
            <dt>Setor principal</dt><dd>{{ $usuario->setor->nome_setor ?? 'N/A' }}</dd>
            <dt>Status no sistema</dt><dd>{{ $usuario->status ? 'Ativo' : 'Inativo' }}</dd>
        </dl>
        <a class="button" href="{{ route('users.edit', $usuario->id_usuario) }}">Editar dados</a>
    </section>
@endsection
