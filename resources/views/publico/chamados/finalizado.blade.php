@extends('publico.layouts.app')

@section('titulo', 'Chamado Finalizado')

@section('conteudo')
<div class="mx-auto w-full max-w-3xl space-y-4 sm:space-y-6">
    <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-8">
        <h1 class="text-2xl font-bold text-primary-600">Chamado Finalizado</h1>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-sm text-slate-500">Protocolo</p>
                <p class="font-bold text-primary-600">{{ $chamado->protocolo }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Situação Atual</p>
                <x-badge-status :status="$chamado->status" tamanho="md" />
            </div>
            <div>
                <p class="text-sm text-slate-500">Data de Abertura</p>
                <p>{{ $chamado->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Data de Finalização</p>
                <p>{{ $chamado->finalizado_em?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
        </div>
    </div>

        <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-8">
            <h2 class="mb-4 text-lg font-semibold text-primary-600">Histórico Público</h2>
        @forelse ($chamado->historicosPublicos as $historico)
            <div class="border-s-4 border-primary-500 ps-4 pb-4">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="font-medium">{{ $historico->tecnico->nome }}</p>
                    <x-badge-status :status="$historico->status" />
                </div>
                <p class="mt-1 text-sm text-slate-600">{{ $historico->descricao }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $historico->created_at->format('d/m/Y H:i') }}</p>
            </div>
        @empty
            <p class="text-slate-500">Nenhuma atualização pública disponível.</p>
        @endforelse
    </div>

    @if ($chamado->avaliacao)
        <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-8">
            <h2 class="mb-4 text-lg font-semibold text-primary-600">Sua Avaliação</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-sm text-slate-500">Satisfação</p>
                    <p class="text-2xl font-bold text-primary-600">{{ $chamado->avaliacao->nota_satisfacao }}/5</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Tempo de Resolução</p>
                    <p class="text-2xl font-bold text-primary-600">{{ $chamado->avaliacao->nota_tempo_resolucao }}/5</p>
                </div>
            </div>
            @if ($chamado->avaliacao->comentario)
                <p class="mt-4 text-slate-600">{{ $chamado->avaliacao->comentario }}</p>
            @endif
        </div>
    @endif
</div>
@endsection
