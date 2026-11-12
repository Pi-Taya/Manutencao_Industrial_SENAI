<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Fabricante</title>
</head>
<body>
    <h1>Editar Fabricante</h1>
    <a href="{{ route('fabricantes.index') }}">Voltar à Lista</a>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('fabricantes.update', $fabricante->id_fabricante) }}" method="POST" style="margin-top: 15px;">
        @csrf
        @method('PUT')
        <div>
            <label for="nome_fabricante">Nome do Fabricante *:</label><br>
            <input type="text" id="nome_fabricante" name="nome_fabricante" value="{{ old('nome_fabricante', $fabricante->nome_fabricante) }}" required>
        </div>
        <br>
        <div>
            <label for="telefone">Telefone:</label><br>
            <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $fabricante->telefone) }}">
        </div>
        <br>
        <div>
            <label for="email">E-mail de Contato:</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $fabricante->email) }}">
        </div>
        <br>
        <button type="submit">Atualizar Fabricante</button>
        <a href="{{ route('fabricantes.index') }}">Cancelar</a>
    </form>
</body>
</html>