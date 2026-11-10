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

    <table border="1" cellpadding="8" style="margin-top: 15px; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Modelo</th>
                <th>Setor</th>
                <th>Fabricante</th>
                <th>Data de Aquisição</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maquinas as $maquina)
                <tr>
                    <td>{{ $maquina->codigo_identificador }}</td>
                    <td>{{ $maquina->nome }}</td>
                    <td>{{ $maquina->modelo ?? 'N/A' }}</td>
                    <td>{{ $maquina->setor->nome_setor ?? 'N/A' }}</td>
                    <td>{{ $maquina->fabricante ?? 'N/A' }}</td>
                    <td>{{ $maquina->data_aquisicao ? \Carbon\Carbon::parse($maquina->data_aquisicao)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $maquina->statusMaquina->nome_status ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhuma máquina cadastrada até o momento.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $maquinas->links() }}
</body>
</html>