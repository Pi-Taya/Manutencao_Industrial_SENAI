<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Ordens de Serviço</title>
</head>
<body>
    <h1>Painel de Ordens de Serviço</h1>
    <p>
        <a href="{{ route('dashboard') }}">Voltar ao Dashboard</a> | 
        <a href="{{ route('ordens.create') }}"><strong>+ Nova Ordem de Serviço</strong></a>
    </p>

    @if(session('success'))
        <p><strong>[Sucesso]: {{ session('success') }}</strong></p>
    @endif

    <hr>

    <!-- Filtros de Busca -->
    <form action="{{ route('ordens.index') }}" method="GET">
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="">Todos</option>
            @foreach(['Aberta', 'Aguardando Aprovação', 'Em Andamento', 'Aguardando Peça', 'Aguardando Validação', 'Concluída', 'Cancelada'] as $st)
                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
        </select>

        <label for="id_status_prioridade">Prioridade:</label>
        <select name="id_status_prioridade" id="id_status_prioridade">
            <option value="">Todas</option>
            @foreach($prioridades as $prio)
                <option value="{{ $prio->id_status_prioridade }}" {{ request('id_status_prioridade') == $prio->id_status_prioridade ? 'selected' : '' }}>
                    {{ $prio->nome_prioridade }}
                </option>
            @endforeach
        </select>

        <button type="submit">Filtrar</button>
        <a href="{{ route('ordens.index') }}">Limpar</a>
    </form>

    <hr>

    <table border="1" cellpadding="6" cellspacing="0">
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
                    <td>{{ $os->data_abertura->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('ordens.show', $os->id_ordem_servico) }}">Ver Detalhes / Gerenciar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Nenhuma Ordem de Serviço registrada no momento.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <div>
        {{ $ordens->links() }}
    </div>
</body>
</html>