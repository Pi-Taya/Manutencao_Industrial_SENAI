<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário / Técnico</title>
</head>
<body>
    <h1>Editar Usuário / Técnico</h1>
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

    <form action="{{ route('users.update', $usuario->id_usuario) }}" method="POST" style="margin-top: 15px;">
        @csrf
        @method('PUT')

        <div>
            <label for="nome_usuario">Nome Completo *:</label><br>
            <input type="text" id="nome_usuario" name="nome_usuario" value="{{ old('nome_usuario', $usuario->nome_usuario) }}" required>
        </div>
        <br>

        <div>
            <label for="email">E-mail *:</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
        </div>
        <br>

        <div>
            <label for="senha">Nova Senha (deixe em branco para não alterar):</label><br>
            <input type="password" id="senha" name="senha">
        </div>
        <br>

        <div>
            <label for="id_perfil">Perfil de Acesso *:</label><br>
            <select id="id_perfil" name="id_perfil" required>
                @foreach($perfis as $perfil)
                    <option value="{{ $perfil->id_perfil }}" {{ old('id_perfil', $usuario->id_perfil) == $perfil->id_perfil ? 'selected' : '' }}>
                        {{ $perfil->tipo_perfil }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label for="especialidade">Especialidade (para Técnicos):</label><br>
            <select id="especialidade" name="especialidade">
                @foreach(['Geral', 'Mecânica', 'Elétrica', 'Hidráulica', 'Pneumática', 'Automação'] as $esp)
                    <option value="{{ $esp }}" {{ old('especialidade', $usuario->especialidade) == $esp ? 'selected' : '' }}>
                        {{ $esp }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label for="id_turno">Turno *:</label><br>
            <select id="id_turno" name="id_turno" required>
                @foreach($turnos as $turno)
                    <option value="{{ $turno->id_turno }}" {{ old('id_turno', $usuario->id_turno) == $turno->id_turno ? 'selected' : '' }}>
                        {{ $turno->nome_turno }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label for="id_setor">Setor Principal *:</label><br>
            <select id="id_setor" name="id_setor" required>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id_setor }}" {{ old('id_setor', $usuario->id_setor) == $setor->id_setor ? 'selected' : '' }}>
                        {{ $setor->nome_setor }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label for="status">Status do Usuário *:</label><br>
            <select id="status" name="status" required>
                <option value="1" {{ old('status', $usuario->status) == 1 ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ old('status', $usuario->status) == 0 ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>
        <br>

        <button type="submit">Atualizar Usuário</button>
        <a href="{{ route('users.index') }}">Cancelar</a>
    </form>
</body>
</html>