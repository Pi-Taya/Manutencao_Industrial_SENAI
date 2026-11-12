<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário / Técnico</title>
</head>
<body>
    <h1>Cadastrar Usuário</h1>
    <a href="{{ route('users.index') }}">Voltar à Lista</a>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" style="margin-top: 15px;">
        @csrf

        <div>
            <label for="nome_usuario">Nome Completo *:</label><br>
            <input type="text" id="nome_usuario" name="nome_usuario" value="{{ old('nome_usuario') }}" required>
        </div>
        <br>

        <div>
            <label for="email">E-mail *:</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <br>

        <div>
            <label for="senha">Senha *:</label><br>
            <input type="password" id="senha" name="senha" required>
        </div>
        <br>

        <div>
            <label for="id_perfil">Perfil de Acesso *:</label><br>
            <select id="id_perfil" name="id_perfil" required>
                <option value="">Selecione o Perfil</option>
                @foreach($perfis as $perfil)
                    <option value="{{ $perfil->id_perfil }}">{{ $perfil->tipo_perfil }}</option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label for="especialidade">Especialidade (para Técnicos):</label><br>
            <select id="especialidade" name="especialidade">
                <option value="Geral">Geral</option>
                <option value="Mecânica">Mecânica</option>
                <option value="Elétrica">Elétrica</option>
                <option value="Hidráulica">Hidráulica</option>
                <option value="Pneumática">Pneumática</option>
                <option value="Automação">Automação</option>
            </select>
        </div>
        <br>

        <div>
            <label for="id_turno">Turno *:</label><br>
            <select id="id_turno" name="id_turno" required>
                <option value="">Selecione o Turno</option>
                @foreach($turnos as $turno)
                    <option value="{{ $turno->id_turno }}">{{ $turno->nome_turno }}</option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label for="id_setor">Setor Principal *:</label><br>
            <select id="id_setor" name="id_setor" required>
                <option value="">Selecione o Setor</option>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id_setor }}">{{ $setor->nome_setor }}</option>
                @endforeach
            </select>
        </div>
        <br>

        <button type="submit">Cadastrar Usuário</button>
        <a href="{{ route('users.index') }}">Cancelar</a>
    </form>
</body>
</html>