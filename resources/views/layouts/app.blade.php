<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção Industrial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Manutenção Industrial</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @php($perfilAtual = auth()->user()->perfil->tipo_perfil ?? '')
                    @if(in_array($perfilAtual, ['Técnico', 'Gestor', 'PCM']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('ordens.index') }}">Ordens de Serviço</a>
                        </li>
                    @endif
                    @if(in_array($perfilAtual, ['Técnico', 'Gestor', 'Administrador']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('maquinas.index') }}">Máquinas</a>
                        </li>
                    @endif
                    @if(in_array($perfilAtual, ['Gestor', 'Administrador']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">Cadastros</a>
                        </li>
                    @endif
                </ul>
                <form action="{{ route('logout') }}" method="POST" class="d-flex">
                    @csrf
                    <button class="btn btn-outline-light btn-sm" type="submit">Sair</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>