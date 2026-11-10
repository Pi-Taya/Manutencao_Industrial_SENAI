<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Máquina</title>
</head>
<body>
    <h1>Cadastrar Nova Máquina</h1>
    <a href="{{ route('maquinas.index') }}">Voltar à Lista de Máquinas</a>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('maquinas.store') }}" method="POST" style="margin-top: 15px;">
        @csrf

        <div>
            <label for="codigo_tag">Código TAG *:</label><br>
            <input type="text" id="codigo_tag" name="codigo_tag" value="{{ old('codigo_tag') }}" required>
        </div>
        <br>

        <div>
            <label for="nome_maquina">Nome da Máquina *:</label><br>
            <input type="text" id="nome_maquina" name="nome_maquina" value="{{ old('nome_maquina') }}" required>
        </div>
        <br>

        <div>
            <label for="id_modelo">Modelo *:</label><br>
            <select id="id_modelo" name="id_modelo" required>
                <option value="">Selecione o Modelo</option>
                @foreach($modelos as $modelo)
                    <option value="{{ $modelo->id_modelo }}" {{ old('id_modelo') == $modelo->id_modelo ? 'selected' : '' }}>
                        {{ $modelo->nome_modelo }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('modelos.create') }}" target="_blank">+ Novo Modelo</a>
        </div>
        <br>

        <div>
            <label for="id_fabricante">Fabricante *:</label><br>
            <select id="id_fabricante" name="id_fabricante" required>
                <option value="">Selecione o Fabricante</option>
                @foreach($fabricantes as $fabricante)
                    <option value="{{ $fabricante->id_fabricante }}" {{ old('id_fabricante') == $fabricante->id_fabricante ? 'selected' : '' }}>
                        {{ $fabricante->nome_fabricante }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('fabricantes.create') }}" target="_blank">+ Novo Fabricante</a>
        </div>
        <br>

        <div>
            <label for="data_aquisicao">Data de Aquisição *:</label><br>
            <input type="date" id="data_aquisicao" name="data_aquisicao" value="{{ old('data_aquisicao') }}" required>
        </div>
        <br>

        <div>
            <label for="id_setor">Setor *:</label><br>
            <select id="id_setor" name="id_setor" required>
                <option value="">Selecione o Setor</option>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id_setor }}" {{ old('id_setor') == $setor->id_setor ? 'selected' : '' }}>
                        {{ $setor->nome_setor }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('setores.create') }}" target="_blank">+ Novo Setor</a>
        </div>
        <br>

        <div>
            <label for="id_status_maquina">Status Inicial *:</label><br>
            <select id="id_status_maquina" name="id_status_maquina" required>
                <option value="">Selecione o Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id_status_maquina }}" {{ old('id_status_maquina') == $status->id_status_maquina ? 'selected' : '' }}>
                        {{ $status->estado_da_maquina }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <button type="submit">Salvar Máquina</button>
        <a href="{{ route('maquinas.index') }}">Cancelar</a>
    </form>
</body>
</html>