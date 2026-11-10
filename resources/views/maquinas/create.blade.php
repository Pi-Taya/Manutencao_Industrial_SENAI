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
            <label>Código TAG *:</label><br>
            <input type="text" name="codigo_tag" value="{{ old('codigo_tag') }}" required>
        </div><br>

        <div>
            <label>Nome da Máquina *:</label><br>
            <input type="text" name="nome_maquina" value="{{ old('nome_maquina') }}" required>
        </div><br>

        <div>
            <label>Modelo *:</label><br>
            <select name="id_modelo" required>
                <option value="">Selecione o Modelo</option>
                @foreach($modelos as $modelo)
                    <option value="{{ $modelo->id_modelo }}">{{ $modelo->nome_modelo }}</option>
                @endforeach
            </select>
        </div><br>

        <div>
            <label>Fabricante *:</label><br>
            <select name="id_fabricante" required>
                <option value="">Selecione o Fabricante</option>
                @foreach($fabricantes as $fab)
                    <option value="{{ $fab->id_fabricante }}">{{ $fab->nome_fabricante }}</option>
                @endforeach
            </select>
        </div><br>

        <div>
            <label>Data de Aquisição *:</label><br>
            <input type="date" name="data_aquisicao" value="{{ old('data_aquisicao') }}" required>
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
                    <option value="{{ $status->id_status_maquina }}">{{ $status->estado_da_maquina }}</option>
                @endforeach
            </select>
        </div><br>

        <button type="submit">Salvar Máquina</button>
        <a href="{{ route('maquinas.index') }}">Cancelar</a>
    </form>
</body>
</html>