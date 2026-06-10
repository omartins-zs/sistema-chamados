@extends('publico.layouts.app')

@section('titulo', 'Chamado Registrado')

@section('conteudo')
<div class="card-publico mx-auto w-full max-w-2xl text-center">
    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>

    <h1 class="titulo-pagina text-2xl">Chamado registrado com sucesso!</h1>
    <p class="texto-corpo mt-2">Guarde o protocolo abaixo para acompanhar sua solicitação.</p>

    <div class="info-item-publico mt-8 space-y-4 text-left">
        <div>
            <p class="eyebrow-publico">Protocolo</p>
            <p class="texto-protocolo text-xl" data-testid="protocolo-chamado">{{ $chamado->protocolo }}</p>
        </div>
        <div>
            <p class="eyebrow-publico">Solicitante</p>
            <p class="titulo-pagina font-medium">{{ $chamado->nome_solicitante }}</p>
        </div>
        <div>
            <p class="eyebrow-publico">Situação Atual</p>
            <x-badge-status :status="$chamado->status" tamanho="md" />
        </div>
        <div>
            <p class="eyebrow-publico">Setor</p>
            <p class="titulo-pagina font-medium">{{ $chamado->setor->nome }}</p>
        </div>
        <div>
            <p class="eyebrow-publico">Data de Abertura</p>
            <p class="titulo-pagina font-medium">{{ $chamado->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('chamados.criar') }}" class="btn-primario mt-8 w-full sm:w-auto">
        Abrir Novo Chamado
    </a>
</div>
@endsection
