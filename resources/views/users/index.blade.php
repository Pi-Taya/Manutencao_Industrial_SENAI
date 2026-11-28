@extends('layouts.app')

@section('title', 'Usuários - PITAYA Control')

@section('content')
    <h1>Gestão de Usuários</h1>
    <div class="page-actions">
        <a class="button" href="{{ route('users.create') }}">Cadastrar usuário</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="GET" action="{{ route('users.index') }}" class="card">
        <div class="form-grid">
            <div class="form-field">
                <label for="search">Busca</label>
                <input type="text" id="search" name="search" placeholder="Nome ou e-mail" value="{{ request('search') }}">
            </div>

            @if(auth()->user()->perfil->tipo_perfil === 'Administrador')
                <div class="form-field">
                    <label for="perfil_id">Perfil</label>
                    <select id="perfil_id" name="perfil_id">
                        <option value="">Todos os perfis</option>
                        @foreach($perfis as $p)
                            <option value="{{ $p->id_perfil }}" {{ request('perfil_id') == $p->id_perfil ? 'selected' : '' }}>
                                {{ $p->tipo_perfil }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="form-field">
                <label for="especialidade">Especialidade</label>
                <select id="especialidade" name="especialidade">
                    <option value="">Todas</option>
                    @foreach(['Geral', 'Mecânica', 'Elétrica', 'Hidráulica'] as $esp)
                        <option value="{{ $esp }}" {{ request('especialidade') == $esp ? 'selected' : '' }}>{{ $esp }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit">Filtrar</button>
            <a class="button button-secondary" href="{{ route('users.index') }}">Limpar filtros</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th><th>E-mail</th><th>Perfil</th><th>Especialidade</th>
                    <th>Turno</th><th>Setor</th><th>Status</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                    <tr>
                        <td>{{ $u->nome_usuario }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->perfil->tipo_perfil ?? 'N/A' }}</td>
                        <td>{{ $u->especialidade ?? 'N/A' }}</td>
                        <td>{{ $u->turno->nome_turno ?? 'N/A' }}</td>
                        <td>{{ $u->setor->nome_setor ?? 'N/A' }}</td>
                        <td><span class="badge {{ $u->status ? 'badge-success' : 'badge-danger' }}">{{ $u->status ? 'Ativo' : 'Inativo' }}</span></td>
                        <td>
                            <a href="{{ route('users.show', $u->id_usuario) }}">Visualizar</a>
                            <a href="{{ route('users.edit', $u->id_usuario) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="8">Nenhum usuário encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $usuarios->links() }}
@endsection
