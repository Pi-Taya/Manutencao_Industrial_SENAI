@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Painel de Ordens de Serviço</h2>
        <a href="{{ route('ordens.create') }}" class="btn btn-primary">+ Nova Ordem de Serviço</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th># OS</th>
                        <th>Máquina</th>
                        <th>Tipo</th>
                        <th>Prioridade</th>
                        <th>Solicitante</th>
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
                            <td>
                                <span class="badge 
                                    @if($os->prioridade->nome_prioridade == 'Crítica') bg-danger 
                                    @elseif($os->prioridade->nome_prioridade == 'Alta') bg-warning text-dark 
                                    @elseif($os->prioridade->nome_prioridade == 'Média') bg-info text-dark 
                                    @else bg-secondary @endif">
                                    {{ $os->prioridade->nome_prioridade ?? 'Padrão' }}
                                </span>
                            </td>
                            <td>{{ $os->solicitante->nome_usuario ?? 'Sistema' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $os->status }}</span>
                                @if($os->parou_producao)
                                    <span class="badge bg-danger">PARADA</span>
                                @endif
                            </td>
                            <td>{{ $os->data_abertura->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('ordens.show', $os->id_ordem_servico) }}" class="btn btn-sm btn-outline-primary">Ver Detalhes</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Nenhuma Ordem de Serviço registrada no momento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $ordens->links() }}
            </div>
        </div>
    </div>
</div>
@endsection