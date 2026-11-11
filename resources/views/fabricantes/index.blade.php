<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Fabricantes</title>
</head>
<body>
    <h1>Fabricantes de Equipamentos</h1>
    <a href="{{ route('fabricantes.create') }}">+ Cadastrar Novo Fabricante</a> | 
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
                <th>Nome do Fabricante</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fabricantes as $fabricante)
                <tr>
                    <td>{{ $fabricante->id_fabricante }}</td>
                    <td>{{ $fabricante->nome_fabricante }}</td>
                    <td>{{ $fabricante->telefone ?? 'N/A' }}</td>
                    <td>{{ $fabricante->email ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('fabricantes.edit', $fabricante->id_fabricante) }}">Editar</a> | 
                        <form action="{{ route('fabricantes.destroy', $fabricante->id_fabricante) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este fabricante?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red; background: none; border: none; cursor: pointer; text-decoration: underline;">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhum fabricante cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $fabricantes->links() }}
</body>
</html>