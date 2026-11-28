@extends('layouts.app')

@section('title', 'Ordens de Serviço - PITAYA Control')

@section('content')
    <h1>Painel de Ordens de Serviço</h1>
    <p class="page-actions">
        <a class="button" href="{{ route('ordens.create') }}">Nova Ordem de Serviço</a>
    </p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ route('ordens.index') }}" method="GET" class="card">
        <div class="form-grid">
            <div class="form-field">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="">Todos</option>
                    @foreach(['Aberta', 'Aguardando Aprovação', 'Em Andamento', 'Aguardando Peça', 'Aguardando Validação', 'Concluída', 'Cancelada'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="id_status_prioridade">Prioridade</label>
                <select name="id_status_prioridade" id="id_status_prioridade">
                    <option value="">Todas</option>
                    @foreach($prioridades as $prio)
                        <option value="{{ $prio->id_status_prioridade }}" {{ request('id_status_prioridade') == $prio->id_status_prioridade ? 'selected' : '' }}>{{ $prio->nome_prioridade }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit">Filtrar</button>
            <a class="button button-secondary" href="{{ route('ordens.index') }}">Limpar</a>
        </div>
    </form>

    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th># OS</th>
                <th>Máquina</th>
                <th>Tipo</th>
                <th>Prioridade</th>
                <th>Solicitante</th>
                <th>Técnicos Alocados</th>
                <th>Status</th>
                <th>Abertura</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ordens as $os)
                <tr>
                    <td><strong>#{{ $os->id_ordem_servico }}</strong></td>
                    <td>{{ $os->maquina->nome_maquina ?? 'N/A' }}</td>
                    <td>{{ $os->tipoManutencao->nome_manutencao ?? 'N/A' }}</td>
                    <td>{{ $os->prioridade->nome_prioridade ?? 'Padrão' }}</td>
                    <td>{{ $os->solicitante->nome_usuario ?? 'Sistema' }}</td>
                    <td>
                        @if($os->tecnicos->count() > 0)
                            {{ $os->tecnicos->pluck('nome_usuario')->join(', ') }}
                        @else
                            <em>Não alocado</em>
                        @endif
                    </td>
                    <td>
                        {{ $os->status }}
                        @if($os->parou_producao)
                            <strong>[PARADA]</strong>
                        @endif
                    </td>
                    <td>{{ $os->data_abertura?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('ordens.show', $os->id_ordem_servico) }}">Ver Detalhes / Gerenciar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="empty-state" colspan="9">Nenhuma Ordem de Serviço registrada no momento.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{ $ordens->links() }}
@endsection