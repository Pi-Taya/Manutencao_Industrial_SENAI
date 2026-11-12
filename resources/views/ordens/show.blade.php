<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço #{{ $ordem->id_ordem_servico }}</title>
</head>
<body>
    <h1>Ordem de Serviço #{{ $ordem->id_ordem_servico }}</h1>
    <p><a href="{{ route('ordens.index') }}">Voltar para a Lista</a> | <a href="{{ route('dashboard') }}">Dashboard</a></p>

    @if(session('success'))
        <p><strong>[Sucesso]: {{ session('success') }}</strong></p>
    @endif

    <hr>

    <h2>Status e Situação</h2>
    <ul>
        <li><strong>Status Atual:</strong> {{ $ordem->status }}</li>
        <li><strong>Parou Produção:</strong> {{ $ordem->parou_producao ? 'SIM (Máquina Parada)' : 'NÃO' }}</li>
        <li><strong>Prioridade:</strong> {{ $ordem->prioridade->nome_prioridade ?? 'N/A' }}</li>
        <li><strong>Tipo de Manutenção:</strong> {{ $ordem->tipoManutencao->nome_manutencao ?? 'N/A' }}</li>
    </ul>

    <!-- Formulário de Alteração de Status (RF09) -->
    <form action="{{ route('ordens.status', $ordem->id_ordem_servico) }}" method="POST">
        @csrf
        @method('PATCH')
        <label for="status"><strong>Alterar Status da OS:</strong></label>
        <select name="status" id="status">
            @foreach(['Aberta', 'Aguardando Aprovação', 'Em Andamento', 'Aguardando Peça', 'Aguardando Validação', 'Concluída', 'Cancelada'] as $st)
                <option value="{{ $st }}" {{ $ordem->status == $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
        </select>
        <button type="submit">Atualizar Status</button>
    </form>

    <hr>

    <h2>Dados do Equipamento e Tempos</h2>
    <ul>
        <li><strong>Máquina:</strong> {{ $ordem->maquina->nome_maquina ?? 'N/A' }} (Cód: {{ $ordem->maquina->codigo_maquina ?? 'N/A' }})</li>
        <li><strong>Setor:</strong> {{ $ordem->maquina->setor->nome_setor ?? 'N/A' }}</li>
        <li><strong>Solicitante:</strong> {{ $ordem->solicitante->nome_usuario ?? 'N/A' }}</li>
        <li><strong>Data/Hora Abertura:</strong> {{ $ordem->data_abertura->format('d/m/Y H:i') }}</li>
        <li><strong>Previsão Limite (SLA):</strong> {{ $ordem->data_previsao->format('d/m/Y H:i') }}</li>
        <li><strong>Início Atendimento:</strong> {{ $ordem->data_inicio_atendimento ? $ordem->data_inicio_atendimento->format('d/m/Y H:i') : 'Não iniciado' }}</li>
        <li><strong>Data Fechamento:</strong> {{ $ordem->data_fechamento ? $ordem->data_fechamento->format('d/m/Y H:i') : 'Em aberto' }}</li>
        @if(!is_null($ordem->tempo_parada_minutos))
            <li><strong>Tempo Total de Atendimento/Parada:</strong> {{ $ordem->tempo_parada_minutos }} minutos</li>
        @endif
    </ul>

    <h3>Descrição da Falha</h3>
    <p>{{ $ordem->descricao_falha }}</p>

    <hr>

    <h2>Técnicos Alocados (RF08)</h2>
    @if($ordem->tecnicos->count() > 0)
        <ul>
            @foreach($ordem->tecnicos as $tecnico)
                <li>{{ $tecnico->nome_usuario }} - Especialidade: {{ $tecnico->especialidade ?? 'Geral' }} ({{ $tecnico->turno->nome_turno ?? 'Sem turno' }})</li>
            @endforeach
        </ul>
    @else
        <p>Nenhum técnico alocado nesta OS.</p>
    @endif

    <!-- Formulário para alocar técnicos (Gestores/Administradores ou Técnicos) -->
    <form action="{{ route('ordens.alocar', $ordem->id_ordem_servico) }}" method="POST">
        @csrf
        <p><strong>Selecionar Técnicos Responsáveis:</strong></p>
        @forelse($tecnicosDisponiveis as $tec)
            <div>
                <label>
                    <input type="checkbox" name="tecnicos[]" value="{{ $tec->id_usuario }}" 
                        {{ $ordem->tecnicos->contains('id_usuario', $tec->id_usuario) ? 'checked' : '' }}>
                    {{ $tec->nome_usuario }} ({{ $tec->especialidade ?? 'Geral' }})
                </label>
            </div>
        @empty
            <p>Nenhum usuário com perfil "Técnico" ativo cadastrado.</p>
        @endforelse
        <br>
        <button type="submit">Salvar Alocação de Técnicos</button>
    </form>

    <hr>
</body>
</html>