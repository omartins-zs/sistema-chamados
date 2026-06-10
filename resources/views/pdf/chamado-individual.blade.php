<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Chamado {{ $chamado->protocolo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0D1B2A; }
        h1 { color: #415A77; font-size: 18px; margin-bottom: 4px; }
        h2 { color: #415A77; font-size: 14px; margin-top: 20px; border-bottom: 1px solid #778DA9; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 6px 8px; border: 1px solid #778DA9; vertical-align: top; }
        th { background: #E0E1DD; color: #1B263B; width: 28%; }
        .historico { margin-bottom: 10px; padding: 8px; border-left: 3px solid #415A77; background: #E0E1DD; }
        .meta { color: #778DA9; font-size: 10px; }
    </style>
</head>
<body>
    <h1>{{ $chamado->protocolo }} — {{ $chamado->titulo }}</h1>
    <p class="meta">Gerado em {{ now()->format('d/m/Y H:i') }}</p>

    <h2>Dados do Solicitante</h2>
    <table>
        <tr><th>Nome</th><td>{{ $chamado->nome_solicitante }}</td></tr>
        <tr><th>E-mail</th><td>{{ $chamado->email_solicitante }}</td></tr>
        <tr><th>Telefone</th><td>{{ $chamado->telefone_solicitante }}</td></tr>
    </table>

    <h2>Detalhes do Chamado</h2>
    <table>
        <tr><th>Status</th><td>{{ $chamado->status->rotulo() }}</td></tr>
        <tr><th>Complexidade</th><td>{{ $chamado->complexidade->rotulo() }}</td></tr>
        <tr><th>Setor</th><td>{{ $chamado->setor->nome }}</td></tr>
        <tr><th>Técnico</th><td>{{ $chamado->tecnicoResponsavel?->nome ?? '—' }}</td></tr>
        <tr><th>Abertura</th><td>{{ $chamado->created_at?->format('d/m/Y H:i') }}</td></tr>
        <tr><th>Finalização</th><td>{{ $chamado->finalizado_em?->format('d/m/Y H:i') ?? '—' }}</td></tr>
        <tr><th>Descrição</th><td>{{ $chamado->descricao }}</td></tr>
    </table>

    @if($chamado->avaliacao)
        <h2>Avaliação</h2>
        <table>
            <tr><th>Satisfação</th><td>{{ $chamado->avaliacao->nota_satisfacao }} / 5</td></tr>
            <tr><th>Tempo de resolução</th><td>{{ $chamado->avaliacao->nota_tempo_resolucao }} / 5</td></tr>
            <tr><th>Comentário</th><td>{{ $chamado->avaliacao->comentario ?? '—' }}</td></tr>
        </table>
    @endif

    <h2>Histórico</h2>
    @forelse($historicos as $historico)
        <div class="historico">
            <strong>{{ $historico->status->rotulo() }}</strong>
            <span class="meta"> — {{ $historico->created_at?->format('d/m/Y H:i') }} · {{ $historico->tecnico?->nome }}</span>
            <p>{{ $historico->descricao }}</p>
        </div>
    @empty
        <p>Nenhum histórico registrado.</p>
    @endforelse
</body>
</html>
