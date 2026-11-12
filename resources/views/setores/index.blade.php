<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Setores</title>
</head>
<body>
    <h1>Setores Fabris</h1>
    <a href="{{ route('setores.create') }}">+ Cadastrar Novo Setor</a> | 
    <a href="{{ route('dashboard') }}">Voltar ao Dashboard</a>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <table border="1" cellpadding="8" style="margin-top: 15px; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Setor</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($setores as $setor)
                <tr>
                    <td>{{ $setor->id_setor }}</td>
                    <td>{{ $setor->nome_setor }}</td>
                    <td>{{ $setor->descricao ?? 'Sem descrição' }}</td>
                    <td>
                        <a href="{{ route('setores.edit', $setor->id_setor) }}">Editar</a> | 
                        <form action="{{ route('setores.destroy', $setor->id_setor) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este setor?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red; background: none; border: none; cursor: pointer; text-decoration: underline;">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhum setor cadastrado até o momento.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $setores->links() }}
</body>
</html>