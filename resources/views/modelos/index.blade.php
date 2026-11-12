<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Modelos</title>
</head>
<body>
    <h1>Modelos de Equipamentos</h1>
    <a href="{{ route('modelos.create') }}">+ Cadastrar Novo Modelo</a> | 
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
                <th>Nome do Modelo</th>
                <th>Fabricante</th>
                <th>Especificações Técnicas</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($modelos as $modelo)
                <tr>
                    <td>{{ $modelo->id_modelo }}</td>
                    <td>{{ $modelo->nome_modelo }}</td>
                    <td>{{ $modelo->fabricante->nome_fabricante ?? 'N/A' }}</td>
                    <td>{{ $modelo->especificacoes_tecnicas ?? 'Sem especificações' }}</td>
                    <td>
                        <a href="{{ route('modelos.edit', $modelo->id_modelo) }}">Editar</a> | 
                        <form action="{{ route('modelos.destroy', $modelo->id_modelo) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este modelo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red; background: none; border: none; cursor: pointer; text-decoration: underline;">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhum modelo cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $modelos->links() }}
</body>
</html>