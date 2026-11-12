<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Setor</title>
</head>
<body>
    <h1>Editar Setor</h1>
    <a href="{{ route('setores.index') }}">Voltar à Lista de Setores</a>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('setores.update', $setor->id_setor) }}" method="POST" style="margin-top: 15px;">
        @csrf
        @method('PUT')
        <div>
            <label for="nome_setor">Nome do Setor *:</label><br>
            <input type="text" id="nome_setor" name="nome_setor" value="{{ old('nome_setor', $setor->nome_setor) }}" required>
        </div>
        <br>
        <div>
            <label for="descricao">Descrição:</label><br>
            <textarea id="descricao" name="descricao" rows="3">{{ old('descricao', $setor->descricao) }}</textarea>
        </div>
        <br>
        <button type="submit">Atualizar Setor</button>
        <a href="{{ route('setores.index') }}">Cancelar</a>
    </form>
</body>
</html>