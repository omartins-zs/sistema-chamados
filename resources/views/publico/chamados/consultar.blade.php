@extends('publico.layouts.app')

@section('titulo', 'Consultar Chamado')

@section('classe_main')
shell-main-centro
@endsection

@section('conteudo')
<div class="mx-auto w-full max-w-xl">
    <div class="mb-5 text-center sm:text-left">
        <p class="eyebrow-publico">Acompanhamento</p>
        <h1 class="titulo-pagina mt-1 text-2xl sm:text-3xl">Consultar Chamado</h1>
        <p class="texto-corpo mt-2 text-sm sm:text-base">
            Informe o protocolo recebido ao abrir o chamado.
        </p>
    </div>

    <form action="{{ route('chamados.consultar.buscar') }}" method="POST" class="card-publico card-publico-compacto space-y-5">
        @csrf

        <div>
            <label for="protocolo" class="label-publico">Protocolo</label>
            <input type="text" id="protocolo" name="protocolo" value="{{ old('protocolo') }}"
                placeholder="CHM-2026-000001"
                class="input-publico uppercase" required>
            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Exemplo: CHM-2026-000001</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
            <a href="{{ route('chamados.criar') }}" class="btn-secundario w-full sm:w-auto">
                Voltar
            </a>
            <button type="submit" data-testid="btn-consultar-chamado" class="btn-primario w-full sm:w-auto">
                Consultar
            </button>
        </div>
    </form>
</div>
@endsection
