@extends('publico.layouts.app')

@section('titulo', 'Chamado Finalizado')

@section('conteudo')
<div class="mx-auto w-full max-w-3xl space-y-4 sm:space-y-6">
    <div class="card-publico">
        <h1 class="titulo-pagina text-2xl">Chamado Finalizado</h1>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div>
                <p class="eyebrow-publico">Protocolo</p>
                <p class="texto-protocolo">{{ $chamado->protocolo }}</p>
            </div>
            <div>
                <p class="eyebrow-publico">Situação Atual</p>
                <x-badge-status :status="$chamado->status" tamanho="md" />
            </div>
            <div>
                <p class="eyebrow-publico">Data de Abertura</p>
                <p class="titulo-pagina">{{ $chamado->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="eyebrow-publico">Data de Finalização</p>
                <p class="titulo-pagina">{{ $chamado->finalizado_em?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
        </div>
    </div>

    <div class="card-publico">
        <h2 class="titulo-pagina mb-4 text-lg">Histórico Público</h2>
        @forelse ($chamado->historicosPublicos as $historico)
            <div class="border-s-4 ps-4 pb-4" style="border-color: var(--palette-mid)">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="titulo-pagina font-medium">{{ $historico->tecnico->nome }}</p>
                    <x-badge-status :status="$historico->status" />
                </div>
                <p class="historico-texto-publico mt-2">{{ $historico->descricao }}</p>
                <p class="texto-corpo mt-1 text-xs">{{ $historico->created_at->format('d/m/Y H:i') }}</p>
            </div>
        @empty
            <p class="texto-corpo">Nenhuma atualização pública disponível.</p>
        @endforelse
    </div>

    @if ($chamado->avaliacao)
        <div class="card-publico">
            <h2 class="titulo-pagina mb-4 text-lg">Sua Avaliação</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="info-item-publico">
                    <p class="eyebrow-publico">Satisfação</p>
                    <p class="titulo-pagina text-2xl">{{ $chamado->avaliacao->nota_satisfacao }}<span class="texto-corpo text-lg font-medium">/5</span></p>
                </div>
                <div class="info-item-publico">
                    <p class="eyebrow-publico">Tempo de Resolução</p>
                    <p class="titulo-pagina text-2xl">{{ $chamado->avaliacao->nota_tempo_resolucao }}<span class="texto-corpo text-lg font-medium">/5</span></p>
                </div>
            </div>
            @if ($chamado->avaliacao->comentario)
                <p class="historico-texto-publico mt-4">{{ $chamado->avaliacao->comentario }}</p>
            @endif
        </div>
    @endif
</div>
@endsection
