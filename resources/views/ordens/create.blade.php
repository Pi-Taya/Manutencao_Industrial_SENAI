@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Abrir Nova Ordem de Serviço</h5>
                    <a href="{{ route('ordens.index') }}" class="btn btn-sm btn-light">Voltar</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('ordens.store') }}" method="POST">
                        @csrf

                        <!-- Máquina -->
                        <div class="mb-3">
                            <label for="id_maquina" class="form-label font-weight-bold">Máquina / Equipamento *</label>
                            <select name="id_maquina" id="id_maquina" class="form-select @error('id_maquina') is-invalid @enderror" required>
                                <option value="">Selecione a máquina...</option>
                                @foreach($maquinas as $maquina)
                                    <option value="{{ $maquina->id_maquina }}" {{ old('id_maquina') == $maquina->id_maquina ? 'selected' : '' }}>
                                        {{ $maquina->nome_maquina }} (Código: {{ $maquina->codigo_maquina }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_maquina')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Tipo de Manutenção -->
                            <div class="col-md-6 mb-3">
                                <label for="id_tipo_manutencao" class="form-label font-weight-bold">Tipo de Manutenção *</label>
                                <select name="id_tipo_manutencao" id="id_tipo_manutencao" class="form-select @error('id_tipo_manutencao') is-invalid @enderror" required>
                                    <option value="">Selecione...</option>
                                    @foreach($tiposManutencao as $tipo)
                                        <option value="{{ $tipo->id_tipo_manutencao }}" {{ old('id_tipo_manutencao') == $tipo->id_tipo_manutencao ? 'selected' : '' }}>
                                            {{ $tipo->nome_manutencao }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_tipo_manutencao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Prioridade -->
                            <div class="col-md-6 mb-3">
                                <label for="id_status_prioridade" class="form-label font-weight-bold">Prioridade *</label>
                                <select name="id_status_prioridade" id="id_status_prioridade" class="form-select @error('id_status_prioridade') is-invalid @enderror" required>
                                    <option value="">Selecione...</option>
                                    @foreach($prioridades as $p)
                                        <option value="{{ $p->id_status_prioridade }}" {{ old('id_status_prioridade') == $p->id_status_prioridade ? 'selected' : '' }}>
                                            {{ $p->nome_prioridade }} (SLA: {{ substr($p->tempo_limite_atendimento, 0, 5) }}h)
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_status_prioridade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Descrição da Falha -->
                        <div class="mb-3">
                            <label for="descricao_falha" class="form-label font-weight-bold">Descrição do Problema / Falha *</label>
                            <textarea name="descricao_falha" id="descricao_falha" rows="4" class="form-control @error('descricao_falha') is-invalid @enderror" placeholder="Descreva os sintomas, ruídos ou falhas observadas..." required>{{ old('descricao_falha') }}</textarea>
                            @error('descricao_falha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Parou Produção (Gatilho da RN04) -->
                        <div class="form-check form-switch mb-4 p-3 bg-light rounded border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="parou_producao" id="parou_producao" value="1" {{ old('parou_producao') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="parou_producao">
                                A falha provocou a parada total da produção nesta máquina? (RN04)
                            </label>
                            <div class="form-text text-muted">
                                Se marcado, o status da máquina será alterado automaticamente para "Em Manutenção".
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('ordens.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Registrar e Abrir OS</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection