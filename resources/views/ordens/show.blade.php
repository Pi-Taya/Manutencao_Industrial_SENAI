<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço #{{ $ordem->id_ordem_servico }}</title>
</head>
<body>
    @php
        $usuarioLogado = Auth::user();
        $perfilUsuario = $usuarioLogado->perfil->tipo_perfil ?? '';
        $isGestor = $perfilUsuario === 'Gestor';
        $isTecnicoAlocado = $ordem->tecnicos->contains('id_usuario', $usuarioLogado->id_usuario);
        $osConcluida = ($ordem->status === 'Concluída');
        $podeExecutarOs = !$osConcluida && ($isGestor || ($perfilUsuario === 'Técnico' && $isTecnicoAlocado));
    @endphp

    <h1>Ordem de Serviço #{{ $ordem->id_ordem_servico }}</h1>
    <p><a href="{{ route('ordens.index') }}">Voltar para a Lista</a> | <a href="{{ route('dashboard') }}">Dashboard</a></p>

    @if($osConcluida)
        <p><strong>Esta Ordem de Serviço está CONCLUÍDA e bloqueada para alterações.</strong></p>
    @endif

    @if(session('success'))
        <p><strong>[Sucesso]: {{ session('success') }}</strong></p>
    @endif

    @if(session('error'))
        <p><strong>[Atenção]: {{ session('error') }}</strong></p>
    @endif

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $erro)
                <li><strong>[Erro]:</strong> {{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <hr>

    <h2>Status e Situação</h2>
    <ul>
        <li><strong>Status Atual:</strong> {{ $ordem->status }}</li>
        <li><strong>Parou Produção:</strong> {{ $ordem->parou_producao ? 'SIM (Máquina Parada)' : 'NÃO' }}</li>
        <li><strong>Prioridade:</strong> {{ $ordem->prioridade->nome_prioridade ?? 'N/A' }}</li>
        <li><strong>Tipo de Manutenção:</strong> {{ $ordem->tipoManutencao->nome_manutencao ?? 'N/A' }}</li>
        @if($ordem->id_chamado)
            <li><strong>Chamado relacionado:</strong> #{{ $ordem->id_chamado }}</li>
        @endif
        <li><strong>Data programada:</strong> {{ $ordem->data_programada ? $ordem->data_programada->format('d/m/Y H:i') : 'Não programada' }}</li>
        @if($ordem->observacoes_pcm)
            <li><strong>Observações do PCM:</strong> {{ $ordem->observacoes_pcm }}</li>
        @endif
    </ul>

    <!-- Formulário de Alteração de Status -->
    @if($podeExecutarOs)
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
    @elseif(!$osConcluida)
        <p><em>Somente técnicos alocados nesta OS ou gestores podem alterar o status.</em></p>
    @endif

    <hr>

    <h2>Dados do Equipamento e Tempos</h2>
    <ul>
        <li><strong>Máquina:</strong> {{ $ordem->maquina->nome_maquina ?? 'N/A' }} (TAG: {{ $ordem->maquina->codigo_tag ?? 'N/A' }})</li>
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

    <h2>Técnicos Alocados</h2>
    @if($ordem->tecnicos->count() > 0)
        <ul>
            @foreach($ordem->tecnicos as $tecnico)
                <li>{{ $tecnico->nome_usuario }} - Especialidade: {{ $tecnico->especialidade ?? 'Geral' }} ({{ $tecnico->turno->nome_turno ?? 'Sem turno' }})</li>
            @endforeach
        </ul>
    @else
        <p>Nenhum técnico alocado nesta OS.</p>
    @endif

    <!-- Formulário para alocar técnicos -->
    @if(!$osConcluida && $isGestor)
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
    @endif

    <hr>

    <h2>Parecer Técnico (Laudo)</h2>

    @if($ordem->parecer)
        <ul>
            <li><strong>Registrado em:</strong> {{ $ordem->parecer->data_registro->format('d/m/Y H:i') }}</li>
            <li><strong>Perfil Responsável:</strong> {{ $ordem->parecer->perfil->tipo_perfil ?? 'N/A' }}</li>
            <li><strong>Condição Pós-Reparo:</strong> <strong>{{ $ordem->parecer->condicao_pos_reparo }}</strong></li>
            <li><strong>Laudo Técnico:</strong> {{ $ordem->parecer->descricao_laudo }}</li>
        </ul>
    @else
        <p>Nenhum parecer técnico registrado para esta Ordem de Serviço.</p>
    @endif

    <!-- Formulário de Parecer Técnico -->
    @if($podeExecutarOs)
        <form action="{{ route('ordens.parecer', $ordem->id_ordem_servico) }}" method="POST">
            @csrf
            <div>
                <label for="condicao_pos_reparo"><strong>Condição Pós-Reparo *:</strong></label><br>
                <select name="condicao_pos_reparo" id="condicao_pos_reparo" required>
                    <option value="">Selecione a condição...</option>
                    @foreach([
                        'Operacional / Aprovado',
                        'Operacional com Restrições / Parcial',
                        'Aguardando Peça',
                        'Condenado / Inoperante'
                    ] as $condicao)
                        <option value="{{ $condicao }}" {{ old('condicao_pos_reparo', $ordem->parecer->condicao_pos_reparo ?? '') == $condicao ? 'selected' : '' }}>
                            {{ $condicao }}
                        </option>
                    @endforeach
                </select>
            </div>
            <br>
            <div>
                <label for="descricao_laudo"><strong>Descrição do Laudo / Serviço Executado *:</strong></label><br>
                <textarea name="descricao_laudo" id="descricao_laudo" rows="4" cols="60" required placeholder="Descreva a causa raiz encontrada, o serviço realizado e observações técnicas...">{{ old('descricao_laudo', $ordem->parecer->descricao_laudo ?? '') }}</textarea>
            </div>
            <br>
            <button type="submit">{{ $ordem->parecer ? 'Atualizar Parecer Técnico' : 'Salvar Parecer Técnico' }}</button>
        </form>
    @endif

    <hr>
</body>
</html>