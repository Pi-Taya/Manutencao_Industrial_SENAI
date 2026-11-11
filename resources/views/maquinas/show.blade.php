<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Máquina</title>
</head>
<body>
    <h1>Detalhes da Máquina: {{ $maquina->nome_maquina }}</h1>
    <a href="{{ route('maquinas.index') }}">Voltar à Lista</a> | 
    <a href="{{ route('maquinas.edit', $maquina->id_maquina) }}">Editar</a>

    <table border="1" cellpadding="8" style="margin-top: 15px; border-collapse: collapse;">
        <tr>
            <th>Código TAG:</th>
            <td>{{ $maquina->codigo_tag }}</td>
        </tr>
        <tr>
            <th>Nome da Máquina:</th>
            <td>{{ $maquina->nome_maquina }}</td>
        </tr>
        <tr>
            <th>Modelo:</th>
            <td>{{ $maquina->modelo->nome_modelo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Fabricante:</th>
            <td>{{ $maquina->fabricante->nome_fabricante ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Setor:</th>
            <td>{{ $maquina->setor->nome_setor ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Data de Aquisição:</th>
            <td>{{ \Carbon\Carbon::parse($maquina->data_aquisicao)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Status:</th>
            <td>{{ $maquina->statusMaquina->estado_da_maquina ?? 'N/A' }}</td>
        </tr>
    </table>
</body>
</html>