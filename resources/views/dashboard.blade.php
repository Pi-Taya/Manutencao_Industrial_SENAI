<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Manutenção Industrial</title>
</head>
<body>
    <h1>Bem-vindo, {{ Auth::user()->nome_usuario }}!</h1>
    <p>Perfil: <strong>{{ Auth::user()->perfil->tipo_perfil ?? 'N/A' }}</strong></p>

    <hr>
    
    <nav>
        <ul>
            <li><a href="{{ route('maquinas.index') }}">Gerenciar Máquinas</a></li>
        </ul>
    </nav>

    <hr>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Sair</button>
    </form>
</body>
</html>