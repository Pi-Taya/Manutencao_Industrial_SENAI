<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Usuários e Técnicos</title>
</head>
<body>
    <h1>Gestão de Usuários</h1>
    <a href="{{ route('users.create') }}">+ Cadastrar Novo Usuário</a> | 
    <a href="{{ route('dashboard') }}">Voltar ao Dashboard</a>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    @if($errors->any())
        <p style="color: red;">{{ $errors->first() }}</p>
    @endif

    <!-- Form de Filtro e Pesquisa -->
    <form method="GET" action="{{ route('users.index') }}" style="margin-top: 15px; margin-bottom: 15px;">
        <input type="text" name="search" placeholder="Buscar por nome ou e-mail..." value="{{ request('search') }}">
        
        @if(auth()->user()->perfil->tipo_perfil === 'Administrador')
            <select name="perfil_id">
                <option value="">Todos os Perfis</option>
                @foreach($perfis as $p)
                    <option value="{{ $p->id_perfil }}" {{ request('perfil_id') == $p->id_perfil ? 'selected' : '' }}>
                        {{ $p->tipo_perfil }}
                    </option>
                @endforeach
            </select>
        @endif

        <!-- Filtro de Especialidades oficial do Modelo Lógico -->
        <select name="especialidade">
            <option value="">Todas Especialidades</option>
            @foreach(['Geral', 'Mecânica', 'Elétrica', 'Hidráulica'] as $esp)
                <option value="{{ $esp }}" {{ request('especialidade') == $esp ? 'selected' : '' }}>{{ $esp }}</option>
            @endforeach
        </select>

        <button type="submit">Filtrar</button>
        <a href="{{ route('users.index') }}">Limpar Filtros</a>
    </form>

    <table border="1" cellpadding="8" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Especialidade</th>
                <th>Turno</th>
                <th>Setor</th>
                <th>Status</th>
                <th>Ações</th>
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
                    <td>{{ $u->status ? 'Ativo' : 'Inativo' }}</td>
                    <td>
                        <a href="{{ route('users.show', $u->id_usuario) }}">Visualizar</a> | 
                        <a href="{{ route('users.edit', $u->id_usuario) }}">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Nenhum usuário encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $usuarios->links() }}
    </div>
</body>
</html>