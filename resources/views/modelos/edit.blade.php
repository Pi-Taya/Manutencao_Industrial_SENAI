<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Modelo</title>
</head>
<body>
    <h1>Editar Modelo</h1>
    <a href="{{ route('modelos.index') }}">Voltar à Lista</a>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('modelos.update', $modelo->id_modelo) }}" method="POST" style="margin-top: 15px;">
        @csrf
        @method('PUT')
        <div>
            <label for="nome_modelo">Nome do Modelo *:</label><br>
            <input type="text" id="nome_modelo" name="nome_modelo" value="{{ old('nome_modelo', $modelo->nome_modelo) }}" required>
        </div>
        <br>
        <div>
            <label for="id_fabricante">Fabricante *:</label><br>
            <select id="id_fabricante" name="id_fabricante" required>
                <option value="">Selecione o Fabricante</option>
                @foreach($fabricantes as $fabricante)
                    <option value="{{ $fabricante->id_fabricante }}" {{ old('id_fabricante', $modelo->id_fabricante) == $fabricante->id_fabricante ? 'selected' : '' }}>
                        {{ $fabricante->nome_fabricante }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>
        <div>
            <label for="especificacoes_tecnicas">Especificações Técnicas:</label><br>
            <textarea id="especificacoes_tecnicas" name="especificacoes_tecnicas" rows="4">{{ old('especificacoes_tecnicas', $modelo->especificacoes_tecnicas) }}</textarea>
        </div>
        <br>
        <button type="submit">Atualizar Modelo</button>
        <a href="{{ route('modelos.index') }}">Cancelar</a>
    </form>
</body>
</html>