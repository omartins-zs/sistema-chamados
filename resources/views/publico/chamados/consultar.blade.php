@extends('publico.layouts.app')

@section('titulo', 'Consultar Chamado')

@section('conteudo')
<div class="mx-auto w-full max-w-xl">
    <div class="mb-6 text-center sm:mb-8">
        <h1 class="text-2xl font-bold text-primary-600 sm:text-3xl">Consultar Chamado</h1>
        <p class="mt-2 text-slate-600">Informe o protocolo recebido ao abrir o chamado.</p>
    </div>

    <form action="{{ route('chamados.consultar.buscar') }}" method="POST"
        class="space-y-6 rounded-2xl bg-white p-4 shadow-lg sm:p-8">
        @csrf

        <div>
            <label for="protocolo" class="mb-2 block text-sm font-medium text-slate-700">Protocolo</label>
            <input type="text" id="protocolo" name="protocolo" value="{{ old('protocolo') }}"
                placeholder="CHM-2026-000001"
                class="block w-full rounded-lg border border-slate-300 p-2.5 uppercase focus:border-primary-500 focus:ring-primary-500" required>
            <p class="mt-1 text-xs text-slate-500">Exemplo: CHM-2026-000001</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
            <a href="{{ route('chamados.criar') }}" class="rounded-lg border border-slate-300 px-6 py-3 text-center font-medium text-slate-700 hover:bg-slate-50">
                Cancelar
            </a>
            <button type="submit" class="w-full rounded-lg bg-primary-500 px-6 py-3 font-medium text-white hover:bg-primary-600 focus:ring-4 focus:ring-primary-300 sm:w-auto">
                Consultar
            </button>
        </div>
    </form>
</div>
@endsection
