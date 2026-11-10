<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Máquina</title>
</head>
<body>
    <h1>Cadastrar Nova Máquina</h1>

    @if($errors->any())
        <div style="color: red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('maquinas.store') }}" method="POST">
        @csrf
        <div>
            <label>Código Identificador *:</label><br>
            <input type="text" name="codigo_identificador" value="{{ old('codigo_identificador') }}" required>
        </div><br>

        <div>
            <label>Nome da Máquina *:</label><br>
            <input type="text" name="nome" value="{{ old('nome') }}" required>
        </div><br>

        <div>
            <label>Modelo:</label><br>
            <input type="text" name="modelo" value="{{ old('modelo') }}">
        </div><br>

        <div>
            <label>Fabricante:</label><br>
            <input type="text" name="fabricante" value="{{ old('fabricante') }}">
        </div><br>

        <div>
            <label>Data de Aquisição:</label><br>
            <input type="date" name="data_aquisicao" value="{{ old('data_aquisicao') }}">
        </div><br>

        <div>
            <label>Setor *:</label><br>
            <select name="id_setor" required>
                <option value="">Selecione o Setor</option>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id_setor }}">{{ $setor->nome_setor }}</option>
                @endforeach
            </select>
        </div><br>

        <div>
            <label>Status Inicial *:</label><br>
            <select name="id_status_maquina" required>
                <option value="">Selecione o Status</option>
                @foreach($statusList as $status)
                    <option value="{{ $status->id_status_maquina }}">{{ $status->nome_status }}</option>
                @endforeach
            </select>
        </div><br>

        <button type="submit">Salvar Máquina</button>
        <a href="{{ route('maquinas.index') }}">Cancelar</a>
    </form>
</body>
</html>