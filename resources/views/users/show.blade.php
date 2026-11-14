<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Usuário</title>
</head>
<body>
    <h1>Detalhes do Usuário</h1>
    <p><a href="{{ route('users.index') }}">Voltar à Lista</a></p>

    <hr>

    <ul>
        <li><strong>Nome:</strong> {{ $usuario->nome_usuario }}</li>
        <li><strong>E-mail:</strong> {{ $usuario->email }}</li>
        <li><strong>Perfil de Acesso:</strong> {{ $usuario->perfil->tipo_perfil ?? 'N/A' }}</li>
        <li><strong>Especialidade:</strong> {{ $usuario->especialidade ?? 'N/A' }}</li>
        <li><strong>Turno de Trabalho:</strong> {{ $usuario->turno->nome_turno ?? 'N/A' }}</li>
        <li><strong>Setor Principal:</strong> {{ $usuario->setor->nome_setor ?? 'N/A' }}</li>
        <li><strong>Status no Sistema:</strong> {{ $usuario->status ? 'Ativo' : 'Inativo' }}</li>
    </ul>

    <p><a href="{{ route('users.edit', $usuario->id_usuario) }}">Editar Dados</a></p>
</body>
</html>