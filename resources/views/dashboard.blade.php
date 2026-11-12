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
            <nav>
        <ul>
            <!-- Acesso geral para Técnicos, Gestores e Administradores -->
            <li><a href="{{ route('ordens.index') }}">Ordens de Serviço</a></li>
            
            <!-- Acesso exclusivo para Gestores e Administradores -->
            @if(in_array(auth()->user()->perfil->tipo_perfil ?? '', ['Gestor', 'Administrador']))
                <li><a href="{{ route('maquinas.index') }}">Gerenciar Máquinas</a></li>
                <li><a href="{{ route('users.index') }}">Gerenciar Usuários / Técnicos</a></li>
                <li><a href="{{ route('setores.index') }}">Gerenciar Setores</a></li>
                <li><a href="{{ route('fabricantes.index') }}">Gerenciar Fabricantes</a></li>
                <li><a href="{{ route('modelos.index') }}">Gerenciar Modelos</a></li>
            @endif
        </ul>
    </nav>
        </ul>
    </nav>

    <hr>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Sair</button>
    </form>
</body>
</html>