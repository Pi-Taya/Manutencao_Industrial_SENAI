<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Abrir Nova Ordem de Serviço</title>
</head>
<body>
    <h1>Abrir Nova Ordem de Serviço</h1>
    <p><a href="{{ route('ordens.index') }}">Voltar para a Lista</a> | <a href="{{ route('dashboard') }}">Dashboard</a></p>

    <hr>

    @if($errors->any())
        <div>
            <strong>Corrija os seguintes erros:</strong>
            <ul>
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
        <hr>
    @endif

    <form action="{{ route('ordens.store') }}" method="POST">
        @csrf

        <!-- Máquina -->
        <div>
            <label for="id_maquina"><strong>Máquina / Equipamento *</strong></label><br>
            <select name="id_maquina" id="id_maquina" required>
                <option value="">Selecione a máquina...</option>
                @foreach($maquinas as $maquina)
                    <option value="{{ $maquina->id_maquina }}" {{ old('id_maquina') == $maquina->id_maquina ? 'selected' : '' }}>
                        {{ $maquina->nome_maquina }} (Código: {{ $maquina->codigo_maquina }})
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <!-- Tipo de Manutenção -->
        <div>
            <label for="id_tipo_manutencao"><strong>Tipo de Manutenção *</strong></label><br>
            <select name="id_tipo_manutencao" id="id_tipo_manutencao" required>
                <option value="">Selecione...</option>
                @foreach($tiposManutencao as $tipo)
                    <option value="{{ $tipo->id_tipo_manutencao }}" {{ old('id_tipo_manutencao') == $tipo->id_tipo_manutencao ? 'selected' : '' }}>
                        {{ $tipo->nome_manutencao }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <!-- Prioridade -->
        <div>
            <label for="id_status_prioridade"><strong>Prioridade *</strong></label><br>
            <select name="id_status_prioridade" id="id_status_prioridade" required>
                <option value="">Selecione...</option>
                @foreach($prioridades as $p)
                    <option value="{{ $p->id_status_prioridade }}" {{ old('id_status_prioridade') == $p->id_status_prioridade ? 'selected' : '' }}>
                        {{ $p->nome_prioridade }} (SLA: {{ substr($p->tempo_limite_atendimento, 0, 5) }}h)
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <!-- Descrição da Falha -->
        <div>
            <label for="descricao_falha"><strong>Descrição do Problema / Falha *</strong></label><br>
            <textarea name="descricao_falha" id="descricao_falha" rows="4" cols="50" placeholder="Descreva os sintomas, ruídos ou falhas observadas..." required>{{ old('descricao_falha') }}</textarea>
        </div>
        <br>

        <!-- Parou Produção -->
        <div>
            <label for="parou_producao">
                <input type="checkbox" name="parou_producao" id="parou_producao" value="1" {{ old('parou_producao') ? 'checked' : '' }}>
                <strong>A falha provocou a parada total da produção nesta máquina?</strong>
            </label><br>
            <small>Se marcado, o status da máquina será alterado automaticamente para "Em Manutenção".</small>
        </div>
        <br>

        <hr>

        <button type="submit">Registrar e Abrir OS</button>
        <a href="{{ route('ordens.index') }}">Cancelar</a>
    </form>
</body>
</html>