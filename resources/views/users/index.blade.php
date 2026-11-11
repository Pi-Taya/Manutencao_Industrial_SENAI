<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Usuários e Técnicos</title>
</head>
<body>
    <h1>Gestão de Usuários e Técnicos</h1>
    <a href="{{ route('users.create') }}">+ Cadastrar Novo Usuário</a> | 
    <a href="{{ route('dashboard') }}">Voltar ao Dashboard</a>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <table border="1" cellpadding="8" style="margin-top: 15px; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Especialidade</th>
                <th>Turno</th>
                <th>Setor</th>
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
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum usuário encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $usuarios->links() }}
    </div>
</body>
</html>