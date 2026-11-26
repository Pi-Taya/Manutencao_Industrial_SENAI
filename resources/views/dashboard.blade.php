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

    @php
        $perfilAtual = Auth::user()->perfil->tipo_perfil ?? '';
        $osCriticasPendentes = collect();

        if (in_array($perfilAtual, ['Gestor', 'PCM', 'Técnico'])) {
            $osCriticasPendentes = \App\Models\OrdemServico::with('maquina')
                ->whereNotIn('status', ['Concluída', 'Cancelada'])
                ->whereHas('prioridade', fn($q) => $q->where('nome_prioridade', 'Crítica'))
                ->orderBy('data_abertura', 'asc')
                ->get();
        }
    @endphp

    @if($osCriticasPendentes->isNotEmpty())
        <section>
            <h2>[ALERTA CRÍTICO]: {{ $osCriticasPendentes->count() }} Ordem(ns) de Serviço Crítica(s) Pendente(s)</h2>
            <ul>
                @foreach($osCriticasPendentes as $critica)
                    <li>
                        <a href="{{ route('ordens.show', $critica->id_ordem_servico) }}">
                            <strong>OS #{{ $critica->id_ordem_servico }}</strong> —
                            {{ $critica->maquina->codigo_tag ?? '' }} ({{ $critica->maquina->nome_maquina ?? 'Máquina' }}) —
                            Status: <strong>{{ $critica->status }}</strong>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
        <hr>
    @endif
    
    <nav>
        <ul>
            @if(in_array($perfilAtual, ['Técnico', 'Gestor', 'PCM', 'Administrador']))
                <li><a href="{{ route('ordens.index') }}">Ordens de Serviço</a></li>
            @endif

            @if(in_array($perfilAtual, ['Técnico', 'Gestor', 'Administrador']))
                <li><a href="{{ route('maquinas.index') }}">Máquinas Cadastradas</a></li>
            @endif

            @if(in_array($perfilAtual, ['Gestor', 'Administrador']))
                <li><a href="{{ route('users.index') }}">Gerenciar Usuários / Técnicos</a></li>
                <li><a href="{{ route('setores.index') }}">Gerenciar Setores</a></li>
                <li><a href="{{ route('fabricantes.index') }}">Gerenciar Fabricantes</a></li>
                <li><a href="{{ route('modelos.index') }}">Gerenciar Modelos</a></li>
                <li><a href="{{ route('tipos-manutencao.index') }}">Gerenciar Tipos de Manutenção</a></li>
            @endif
        </ul>
    </nav>

    <hr>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Sair</button>
    </form>
</body>
</html>