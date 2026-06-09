@extends('publico.layouts.app')

@section('titulo', 'Situação do Chamado')

@section('conteudo')
<div class="mx-auto w-full max-w-3xl space-y-4 sm:space-y-6">
    <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-8">
        <h1 class="text-2xl font-bold text-primary-600">Situação Atual do Chamado</h1>

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
                <p class="text-sm text-slate-500">Solicitante</p>
                <p class="font-medium">{{ $chamado->nome_solicitante }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Setor Responsável</p>
                <p class="font-medium">{{ $chamado->setor->nome }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Título</p>
                <p>{{ $chamado->titulo }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Complexidade</p>
                <x-badge-complexidade :complexidade="$chamado->complexidade" tamanho="md" />
            </div>
            <div>
                <p class="text-sm text-slate-500">Data de Abertura</p>
                <p>{{ $chamado->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Técnico Responsável</p>
                <p>{{ $chamado->tecnicoResponsavel?->nome ?? 'Aguardando atribuição' }}</p>
            </div>
        </div>
    </div>

    @if ($chamado->historicosPublicos->isNotEmpty())
        <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-8">
            <h2 class="mb-4 text-lg font-semibold text-primary-600">Histórico do Chamado</h2>
            @foreach ($chamado->historicosPublicos as $historico)
                <div class="border-s-4 border-primary-500 ps-4 pb-4 last:pb-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-medium">{{ $historico->tecnico->nome }}</p>
                        <x-badge-status :status="$historico->status" />
                    </div>
                    <p class="mt-1 text-sm text-slate-600">{{ $historico->descricao }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $historico->created_at->format('d/m/Y H:i') }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="text-center">
        <a href="{{ route('chamados.consultar') }}" class="text-primary-600 hover:underline">Nova consulta</a>
    </div>
</div>
@endsection
