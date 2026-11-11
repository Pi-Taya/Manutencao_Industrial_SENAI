@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ordem de Serviço #{{ $ordem->id_ordem_servico }}</h5>
                    <div>
                        <span class="badge bg-primary fs-6">{{ $ordem->status }}</span>
                        @if($ordem->parou_producao)
                            <span class="badge bg-danger fs-6 ms-1">PARADA DE PRODUÇÃO</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold">Informações do Equipamento</h6>
                            <p class="mb-1"><strong>Máquina:</strong> {{ $ordem->maquina->nome_maquina ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Código:</strong> {{ $ordem->maquina->codigo_maquina ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Localização:</strong> {{ $ordem->maquina->setor->nome_setor ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold">Informações da Solicitação</h6>
                            <p class="mb-1"><strong>Solicitante:</strong> {{ $ordem->solicitante->nome_usuario ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Tipo de Manutenção:</strong> {{ $ordem->tipoManutencao->nome_manutencao ?? 'N/A' }}</p>
                            <p class="mb-1">
                                <strong>Prioridade / SLA:</strong> 
                                <span class="badge 
                                    @if($ordem->prioridade->nome_prioridade == 'Crítica') bg-danger 
                                    @elseif($ordem->prioridade->nome_prioridade == 'Alta') bg-warning text-dark 
                                    @elseif($ordem->prioridade->nome_prioridade == 'Média') bg-info text-dark 
                                    @else bg-secondary @endif">
                                    {{ $ordem->prioridade->nome_prioridade ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Data / Hora Abertura:</strong> {{ $ordem->data_abertura->format('d/m/Y H:i') }}</p>
                            <p class="mb-1"><strong>Previsão de Término (SLA):</strong> {{ $ordem->data_previsao->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Início do Atendimento:</strong> {{ $ordem->data_inicio_atendimento ? $ordem->data_inicio_atendimento->format('d/m/Y H:i') : 'Não iniciado' }}</p>
                            <p class="mb-1"><strong>Data Fechamento:</strong> {{ $ordem->data_fechamento ? $ordem->data_fechamento->format('d/m/Y H:i') : 'Em aberto' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted fw-bold">Descrição da Falha / Relato Inicial</h6>
                        <div class="p-3 bg-light rounded border">
                            {{ $ordem->descricao_falha }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted fw-bold">Técnicos Alocados (RF08)</h6>
                        @if($ordem->tecnicos->count() > 0)
                            <ul class="list-group">
                                @foreach($ordem->tecnicos as $tecnico)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $tecnico->nome_usuario }} ({{ $tecnico->especialidade }})
                                        <span class="badge bg-secondary">{{ $tecnico->turno->nome_turno ?? '' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-warning mb-0">Nenhum técnico associado a esta Ordem de Serviço até o momento.</div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('ordens.index') }}" class="btn btn-secondary">Voltar para a Lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection