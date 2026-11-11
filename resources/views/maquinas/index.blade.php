<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Máquinas</title>
</head>
<body>
    <h1>Máquinas Cadastradas</h1>

    <a href="{{ route('maquinas.create') }}">+ Cadastrar Nova Máquina</a> | 
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
                <th>Código (TAG)</th>
                <th>Nome</th>
                <th>Modelo</th>
                <th>Setor</th>
                <th>Fabricante</th>
                <th>Data de Aquisição</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maquinas as $maquina)
                <tr>
                    <td>{{ $maquina->codigo_tag }}</td>
                    <td>{{ $maquina->nome_maquina }}</td>
                    <td>{{ $maquina->modelo->nome_modelo ?? 'N/A' }}</td>
                    <td>{{ $maquina->setor->nome_setor ?? 'N/A' }}</td>
                    <td>{{ $maquina->fabricante->nome_fabricante ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($maquina->data_aquisicao)->format('d/m/Y') }}</td>
                    <td>{{ $maquina->statusMaquina->estado_da_maquina ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('maquinas.show', $maquina->id_maquina) }}">Visualizar</a> | 
                        <a href="{{ route('maquinas.edit', $maquina->id_maquina) }}">Editar</a> | 
                        <form action="{{ route('maquinas.destroy', $maquina->id_maquina) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta máquina?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red; background: none; border: none; cursor: pointer; text-decoration: underline;">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Nenhuma máquina cadastrada até o momento.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $maquinas->links() }}
    </div>
</body>
</html>