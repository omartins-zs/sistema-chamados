@extends('publico.layouts.app')

@section('titulo', 'Chamado Registrado')

@section('conteudo')
<div class="mx-auto w-full max-w-2xl rounded-2xl bg-white p-4 text-center shadow-lg sm:p-8">
    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-primary-600">Chamado registrado com sucesso!</h1>
    <p class="mt-2 text-slate-600">Guarde o protocolo abaixo para acompanhar sua solicitação.</p>

    <div class="mt-8 space-y-4 rounded-xl bg-slate-50 p-6 text-left">
        <div>
            <p class="text-sm text-slate-500">Protocolo</p>
            <p class="text-xl font-bold text-primary-600">{{ $chamado->protocolo }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Solicitante</p>
            <p class="font-medium">{{ $chamado->nome_solicitante }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Situação Atual</p>
            <x-badge-status :status="$chamado->status" tamanho="md" />
        </div>
        <div>
            <p class="text-sm text-slate-500">Setor</p>
            <p class="font-medium">{{ $chamado->setor->nome }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Data de Abertura</p>
            <p class="font-medium">{{ $chamado->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('chamados.criar') }}" class="mt-8 inline-block w-full rounded-lg bg-primary-500 px-6 py-3 font-medium text-white hover:bg-primary-600 sm:w-auto">
        Abrir Novo Chamado
    </a>
</div>
@endsection
