<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PITAYA Control')</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    @stack('styles')
</head>
<body>
    @php
        $usuario = Auth::user();
        $perfilAtual = $usuario->perfil->tipo_perfil ?? '';
        $cadastroAtivo = request()->routeIs(
            'maquinas.*',
            'users.*',
            'setores.*',
            'fabricantes.*',
            'modelos.*',
            'tipos-manutencao.*'
        );
    @endphp

    <div class="app-shell">
        <aside class="sidebar">
            <a class="sidebar-logo" href="{{ route('dashboard') }}" aria-label="PITAYA Control - Painel Geral">
                <img src="{{ asset('images/logo_pitaya.png') }}" alt="PITAYA Control">
            </a>

            <nav aria-label="Navegação principal">
                <ul>
                    <li data-nav-item data-nav-label="painel geral dashboard">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="nav-icon" aria-hidden="true">⌂</span>
                            <span>Painel Geral</span>
                        </a>
                    </li>

                    @if(in_array($perfilAtual, ['Gestor', 'PCM', 'Técnico', 'Administrador']))
                        <li data-nav-item data-nav-label="ordens de serviço">
                            <a href="{{ route('ordens.index') }}" class="{{ request()->routeIs('ordens.*') ? 'active' : '' }}">
                                <span class="nav-icon" aria-hidden="true">▤</span>
                                <span>Ordens de Serviço</span>
                            </a>
                        </li>
                    @endif

                    @if($perfilAtual === 'Técnico')
                        <li data-nav-item data-nav-label="máquinas máquinas cadastradas">
                            <a href="{{ route('maquinas.index') }}" class="{{ request()->routeIs('maquinas.*') ? 'active' : '' }}">
                                <span class="nav-icon" aria-hidden="true">⚙</span>
                                <span>Máquinas</span>
                            </a>
                        </li>
                    @endif

                    @if(in_array($perfilAtual, ['Gestor', 'Administrador']))
                        <li data-nav-item data-nav-label="cadastro máquinas usuários setores fabricantes modelos tipos de manutenção">
                            <button
                                type="button"
                                class="nav-drawer-toggle"
                                id="cadastro-menu-toggle"
                                aria-expanded="{{ $cadastroAtivo ? 'true' : 'false' }}"
                                aria-controls="cadastro-menu"
                            >
                                <span class="hamburger-icon" aria-hidden="true"><span></span><span></span><span></span></span>
                                <span>Cadastro</span>
                                <span class="nav-caret" aria-hidden="true"></span>
                            </button>
                            <ul class="nav-drawer {{ $cadastroAtivo ? 'is-open' : '' }}" id="cadastro-menu">
                                <li data-nav-item data-nav-label="máquinas cadastradas">
                                    <a href="{{ route('maquinas.index') }}" class="{{ request()->routeIs('maquinas.*') ? 'active' : '' }}">Máquinas Cadastradas</a>
                                </li>
                                <li data-nav-item data-nav-label="gerenciamento de usuários usuários">
                                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Gerenciamento de Usuários</a>
                                </li>
                                <li data-nav-item data-nav-label="gerenciar setores setores">
                                    <a href="{{ route('setores.index') }}" class="{{ request()->routeIs('setores.*') ? 'active' : '' }}">Gerenciar Setores</a>
                                </li>
                                <li data-nav-item data-nav-label="gerenciar fabricantes fabricantes">
                                    <a href="{{ route('fabricantes.index') }}" class="{{ request()->routeIs('fabricantes.*') ? 'active' : '' }}">Gerenciar Fabricantes</a>
                                </li>
                                <li data-nav-item data-nav-label="gerenciar modelos modelos">
                                    <a href="{{ route('modelos.index') }}" class="{{ request()->routeIs('modelos.*') ? 'active' : '' }}">Gerenciar Modelos</a>
                                </li>
                                <li data-nav-item data-nav-label="gerenciar tipos de manutenção tipos manutenção">
                                    <a href="{{ route('tipos-manutencao.index') }}" class="{{ request()->routeIs('tipos-manutencao.*') ? 'active' : '' }}">Gerenciar Tipos de Manutenção</a>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="sidebar-search">
                <input type="search" id="sidebar-search" placeholder="Buscar..." aria-label="Buscar no menu">
            </div>
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-user-info">
                    <span class="topbar-brand">PITAYA CONTROL</span>
                    — {{ \Illuminate\Support\Str::upper($perfilAtual ?: 'USUÁRIO') }}:
                    {{ \Illuminate\Support\Str::upper($usuario->nome_usuario) }} / ID{{ $usuario->id_usuario }}
                </div>
                <div class="topbar-account">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="button">LOGOUT</button>
                    </form>
                    <span class="online-indicator" role="img" aria-label="Usuário online"></span>
                    <span class="user-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($usuario->nome_usuario, 0, 1)) }}</span>
                </div>
            </header>

            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        (() => {
            const toggle = document.getElementById('cadastro-menu-toggle');
            const menu = document.getElementById('cadastro-menu');
            const search = document.getElementById('sidebar-search');
            const setMenuOpen = (isOpen) => {
                if (!toggle || !menu) {
                    return;
                }

                toggle.setAttribute('aria-expanded', String(isOpen));
                menu.classList.toggle('is-open', isOpen);
            };

            if (toggle && menu) {
                toggle.addEventListener('click', () => {
                    setMenuOpen(toggle.getAttribute('aria-expanded') !== 'true');
                });

                document.addEventListener('click', (event) => {
                    if (!toggle.contains(event.target) && !menu.contains(event.target)) {
                        setMenuOpen(false);
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        setMenuOpen(false);
                    }
                });
            }

            if (search) {
                const normalize = (value) => value
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLocaleLowerCase('pt-BR')
                    .trim();

                search.addEventListener('input', () => {
                    const term = normalize(search.value);
                    document.querySelectorAll('[data-nav-item]').forEach((item) => {
                        const label = normalize(item.dataset.navLabel || item.textContent);
                        item.hidden = term !== '' && !label.includes(term);
                    });

                    if (term !== '') {
                        setMenuOpen(true);
                    }
                });
            }
        })();
    </script>
</body>
</html>
