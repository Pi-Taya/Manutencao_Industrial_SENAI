<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Tipo de Manutenção</title>
</head>
<body>
    <h1>Cadastrar Tipo de Manutenção</h1>
    <p><a href="{{ route('tipos-manutencao.index') }}">Voltar à Lista</a></p>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $erro)
                <li><strong>[Erro]:</strong> {{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('tipos-manutencao.store') }}" method="POST">
        @csrf

        <div>
            <label for="nome_manutencao"><strong>Tipo de Manutenção *:</strong></label><br>
            <input type="text" name="nome_manutencao" id="nome_manutencao" value="{{ old('nome_manutencao') }}" maxlength="100" size="40" required placeholder="Ex: Lubrificação, Calibração, Emergencial...">
        </div>
        <br>

        <div>
            <label for="descricao"><strong>Descrição:</strong></label><br>
            <textarea name="descricao" id="descricao" rows="3" cols="50" maxlength="255" placeholder="Descreva a finalidade deste tipo de manutenção...">{{ old('descricao') }}</textarea>
        </div>
        <br>

        <button type="submit">Salvar Tipo de Manutenção</button>
    </form>
</body>
</html>