<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Manutenção</title>
</head>
<body>
    <h1>Tipos de Manutenção</h1>

    <p>
        <a href="{{ route('tipos-manutencao.create') }}">+ Novo Tipo de Manutenção</a> | 
        <a href="{{ route('dashboard') }}">Voltar ao Dashboard</a>
    </p>

    @if(session('success'))
        <p><strong>[Sucesso]: {{ session('success') }}</strong></p>
    @endif

    @if(session('error'))
        <p><strong>[Atenção]: {{ session('error') }}</strong></p>
    @endif

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo de Manutenção</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tipos as $tipo)
                <tr>
                    <td>{{ $tipo->id_tipo_manutencao }}</td>
                    <td>{{ $tipo->nome_manutencao }}</td>
                    <td>{{ $tipo->descricao ?? 'Sem descrição' }}</td>
                    <td>
                        <a href="{{ route('tipos-manutencao.edit', $tipo->id_tipo_manutencao) }}">Editar</a> | 
                        <form action="{{ route('tipos-manutencao.destroy', $tipo->id_tipo_manutencao) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este tipo de manutenção?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhum tipo de manutenção cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>