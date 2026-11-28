@extends('layouts.app')

@section('title', 'Painel Geral - PITAYA Control')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    @php
        $perfilAtual = Auth::user()->perfil->tipo_perfil ?? '';
        $perfisOperacionais = ['Gestor', 'PCM', 'Técnico'];
        $inicioMes = now()->startOfMonth();
        $fimMes = $inicioMes->copy()->endOfMonth();
        $diasCalendario = array_merge(
            array_fill(0, $inicioMes->dayOfWeek, null),
            range(1, $inicioMes->daysInMonth)
        );
        $ordensProgramadas = collect();
        $ordensDeHoje = collect();
        $agendaPorDia = collect();
        $osCriticasPendentes = collect();
        $quantidadeOsCriticas = 0;

        if (in_array($perfilAtual, $perfisOperacionais)) {
            $ordensProgramadas = \App\Models\OrdemServico::with('maquina')
                ->whereNotIn('status', ['Concluída', 'Cancelada'])
                ->whereBetween('data_programada', [$inicioMes->copy()->startOfDay(), $fimMes->copy()->endOfDay()])
                ->orderBy('data_programada')
                ->get();

            $ordensDeHoje = $ordensProgramadas
                ->filter(fn ($ordem) => $ordem->data_programada?->isToday())
                ->values();

            $agendaPorDia = $ordensProgramadas->groupBy(fn ($ordem) => $ordem->data_programada->day);

            $criticasQuery = \App\Models\OrdemServico::query()
                ->whereNotIn('status', ['Concluída', 'Cancelada'])
                ->whereHas('prioridade', fn ($query) => $query->where('nome_prioridade', 'Crítica'));

            $quantidadeOsCriticas = (clone $criticasQuery)->count();
            $osCriticasPendentes = (clone $criticasQuery)
                ->with('maquina')
                ->orderBy('data_abertura')
                ->limit(5)
                ->get();
        }
    @endphp

    <header class="dashboard-intro">
        <h1>Painel Geral</h1>
        <p class="text-muted">
            Bem-vindo, {{ Auth::user()->nome_usuario }}.
            Perfil: <strong>{{ $perfilAtual ?: 'N/A' }}</strong>
        </p>
    </header>

    <h2 class="dashboard-title-bar">Calendário de Manutenção</h2>

    <div class="dashboard-grid">
        <section class="dashboard-card" aria-labelledby="today-heading">
            <header class="dashboard-card-heading">
                <span class="heading-icon" aria-hidden="true">▣</span>
                <h2 id="today-heading">Hoje</h2>
            </header>

            @if($ordensDeHoje->isNotEmpty())
                <div class="today-list">
                    @foreach($ordensDeHoje as $ordem)
                        <a class="today-item" href="{{ route('ordens.show', $ordem->id_ordem_servico) }}">
                            <strong>OS #{{ $ordem->id_ordem_servico }} · {{ $ordem->maquina->codigo_tag ?? 'Máquina' }}</strong>
                            <span>
                                {{ $ordem->maquina->nome_maquina ?? 'Manutenção programada' }}
                                @if($ordem->data_programada)
                                    · {{ $ordem->data_programada->format('H:i') }}
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="dashboard-empty">Não há manutenções programadas para hoje.</p>
            @endif
        </section>

        <section class="dashboard-card" aria-labelledby="calendar-heading">
            <header class="dashboard-card-heading calendar-month-title">
                <span class="heading-icon" aria-hidden="true">▦</span>
                <h2 id="calendar-heading">{{ \Illuminate\Support\Str::upper($inicioMes->locale(config('app.locale'))->translatedFormat('F Y')) }}</h2>
            </header>

            <div class="calendar-grid" role="grid" aria-label="Calendário mensal">
                @foreach(['D', 'S', 'T', 'Q', 'Q', 'S', 'S'] as $diaSemana)
                    <span class="calendar-weekday" role="columnheader">{{ $diaSemana }}</span>
                @endforeach

                @foreach($diasCalendario as $dia)
                    @if($dia === null)
                        <span class="calendar-empty" aria-hidden="true"></span>
                    @else
                        @php
                            $ordensDoDia = $agendaPorDia->get($dia, collect());
                            $diaAtual = now()->isSameDay($inicioMes->copy()->day($dia));
                        @endphp
                        <span
                            class="calendar-day {{ $diaAtual ? 'is-today' : '' }} {{ $ordensDoDia->isNotEmpty() ? 'has-maintenance' : '' }}"
                            role="gridcell"
                            aria-label="{{ $dia }}{{ $ordensDoDia->isNotEmpty() ? ', manutenção programada' : '' }}"
                            @if($diaAtual) aria-current="date" @endif
                        >{{ $dia }}</span>
                    @endif
                @endforeach
            </div>
        </section>
    </div>

    <section class="alerts-panel" aria-labelledby="alerts-heading">
        <header class="alerts-heading">
            <h2 class="alerts-title" id="alerts-heading">
                <span class="alerts-icon" aria-hidden="true">▲</span>
                Alertas
            </h2>
            <span class="alerts-count">{{ $quantidadeOsCriticas }} OS críticas pendentes</span>
        </header>

        @if($osCriticasPendentes->isNotEmpty())
            <ul class="critical-alert-list">
                @foreach($osCriticasPendentes as $critica)
                    <li>
                        <a class="critical-alert-item" href="{{ route('ordens.show', $critica->id_ordem_servico) }}">
                            <span>
                                OS #{{ $critica->id_ordem_servico }} —
                                {{ $critica->maquina->codigo_tag ?? '' }}
                                ({{ $critica->maquina->nome_maquina ?? 'Máquina' }})
                            </span>
                            <span>{{ $critica->status }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="dashboard-empty">Não há alertas críticos pendentes.</p>
        @endif
    </section>

    <nav class="dashboard-links" aria-label="Atalhos">
        @if(in_array($perfilAtual, ['Técnico', 'Gestor', 'PCM', 'Administrador']))
            <a href="{{ route('ordens.index') }}">Ordens de Serviço</a>
        @endif

        @if(in_array($perfilAtual, ['Administrador', 'Técnico', 'Gestor']))
            <a href="{{ route('maquinas.index') }}">Máquinas Cadastradas</a>
        @endif

        @if(in_array($perfilAtual, ['Gestor', 'Administrador']))
            <a href="{{ route('users.index') }}">Usuários</a>
            <a href="{{ route('setores.index') }}">Setores</a>
            <a href="{{ route('fabricantes.index') }}">Fabricantes</a>
            <a href="{{ route('modelos.index') }}">Modelos</a>
            <a href="{{ route('tipos-manutencao.index') }}">Tipos de Manutenção</a>
        @endif
    </nav>
@endsection
