<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Chamados</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0D1B2A; }
        h1 { color: #415A77; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 6px; border: 1px solid #778DA9; text-align: left; }
        th { background: #1B263B; color: #E0E1DD; }
        tr:nth-child(even) { background: #E0E1DD; }
        .meta { color: #778DA9; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Relatório de Chamados</h1>
    <p class="meta">Gerado em {{ $geradoEm->format('d/m/Y H:i') }} · {{ $chamados->count() }} registro(s)</p>

    <table>
        <thead>
            <tr>
                <th>Protocolo</th>
                <th>Solicitante</th>
                <th>Título</th>
                <th>Setor</th>
                <th>Status</th>
                <th>Abertura</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chamados as $chamado)
                <tr>
                    <td>{{ $chamado->protocolo }}</td>
                    <td>{{ $chamado->nome_solicitante }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($chamado->titulo, 40) }}</td>
                    <td>{{ $chamado->setor->nome }}</td>
                    <td>{{ $chamado->status->rotulo() }}</td>
                    <td>{{ $chamado->created_at?->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
