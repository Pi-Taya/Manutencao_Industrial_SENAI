<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Usuário</title>
</head>
<body>
    <h1>Detalhes do Usuário</h1>
    <a href="{{ route('users.index') }}">Voltar à Lista</a>

    <div style="margin-top: 20px; border: 1px solid #ccc; padding: 15px; width: 400px;">
        <p><strong>Nome:</strong> {{ $usuario->nome_usuario }}</p>
        <p><strong>E-mail:</strong> {{ $usuario->email }}</p>
        <p><strong>Perfil de Acesso:</strong> {{ $usuario->perfil->tipo_perfil ?? 'N/A' }}</p>
        <p><strong>Especialidade:</strong> {{ $usuario->especialidade ?? 'N/A' }}</p>
        <p><strong>Turno de Trabalho:</strong> {{ $usuario->turno->nome_turno ?? 'N/A' }}</p>
        <p><strong>Setor Principal:</strong> {{ $usuario->setor->nome_setor ?? 'N/A' }}</p>
        <p><strong>Status no Sistema:</strong> {{ $usuario->status ? 'Ativo' : 'Inativo' }}</p>
    </div>

    <div style="margin-top: 15px;">
        <a href="{{ route('users.edit', $usuario->id_usuario) }}">Editar Dados</a>
    </div>
</body>
</html>