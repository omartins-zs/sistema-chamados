@php
    $totalHistoricos = $historicos->count();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Cabeçalho do chamado --}}
        <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 text-white shadow-lg">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 space-y-3">
                        <p class="font-mono text-sm tracking-wide text-primary-100">
                            {{ $chamado->protocolo }}
                        </p>
                        <h2 class="text-2xl font-bold leading-tight sm:text-3xl">
                            {{ $chamado->titulo }}
                        </h2>
                        <p class="max-w-3xl text-sm leading-relaxed text-primary-100 sm:text-base">
                            {{ $chamado->descricao }}
                        </p>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2 lg:justify-end">
                        <x-badge-status :status="$chamado->status" tamanho="md" class="!bg-white/20 !text-white !ring-white/30 backdrop-blur" />
                        <x-badge-complexidade :complexidade="$chamado->complexidade" tamanho="md" class="!bg-white/10 !text-white !ring-white/20 backdrop-blur" />
                    </div>
                </div>

                <dl class="mt-6 grid gap-4 border-t border-white/20 pt-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-primary-200">Setor</dt>
                        <dd class="mt-1 text-sm font-semibold">{{ $chamado->setor->nome }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-primary-200">Abertura</dt>
                        <dd class="mt-1 text-sm font-semibold">{{ $chamado->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-primary-200">Finalização</dt>
                        <dd class="mt-1 text-sm font-semibold">
                            {{ $chamado->finalizado_em?->format('d/m/Y H:i') ?? 'Em andamento' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-primary-200">Técnico</dt>
                        <dd class="mt-1 text-sm font-semibold">
                            {{ $chamado->tecnicoResponsavel?->nome ?? 'Aguardando atribuição' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </section>

        {{-- Cards de informações --}}
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Solicitante</h3>
                        <p class="text-sm text-gray-500">Dados de contato para retorno</p>
                    </div>
                </div>

                <dl class="space-y-4">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-800">
                        <dt class="text-sm text-gray-500">Nome</dt>
                        <dd class="text-right text-sm font-semibold text-gray-950 dark:text-white">{{ $chamado->nome_solicitante }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-800">
                        <dt class="text-sm text-gray-500">E-mail</dt>
                        <dd class="text-right text-sm font-medium text-primary-700 dark:text-primary-400">
                            <a href="mailto:{{ $chamado->email_solicitante }}" class="hover:underline">{{ $chamado->email_solicitante }}</a>
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-sm text-gray-500">Telefone</dt>
                        <dd class="text-right text-sm font-semibold text-gray-950 dark:text-white">{{ $chamado->telefone_solicitante }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Resumo do atendimento</h3>
                        <p class="text-sm text-gray-500">Status atual e responsáveis</p>
                    </div>
                </div>

                <dl class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4 dark:border-gray-800">
                        <dt class="text-sm text-gray-500">Status atual</dt>
                        <dd><x-badge-status :status="$chamado->status" /></dd>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4 dark:border-gray-800">
                        <dt class="text-sm text-gray-500">Complexidade</dt>
                        <dd><x-badge-complexidade :complexidade="$chamado->complexidade" /></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-800">
                        <dt class="text-sm text-gray-500">Setor responsável</dt>
                        <dd class="text-right text-sm font-semibold text-gray-950 dark:text-white">{{ $chamado->setor->nome }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-sm text-gray-500">Técnico responsável</dt>
                        <dd class="text-right text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $chamado->tecnicoResponsavel?->nome ?? '—' }}
                        </dd>
                    </div>
                </dl>
            </section>
        </div>

        {{-- Linha do tempo --}}
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-6 flex flex-col gap-2 border-b border-gray-100 pb-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Linha do tempo</h3>
                        <p class="text-sm text-gray-500">Histórico completo de interações e mudanças de status</p>
                    </div>
                </div>
                <span class="inline-flex w-fit items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $totalHistoricos }} {{ $totalHistoricos === 1 ? 'registro' : 'registros' }}
                </span>
            </div>

            @forelse ($historicos as $historico)
                @include('filament.chamados.partials.historico-item', ['historico' => $historico])
            @empty
                <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 px-6 py-12 text-center dark:border-gray-600">
                    <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </span>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhum histórico registrado ainda</p>
                    <p class="mt-1 text-sm text-gray-500">Use o botão <strong>Adicionar Histórico</strong> para registrar a primeira interação.</p>
                </div>
            @endforelse
        </section>

        {{-- Avaliação --}}
        @if ($chamado->avaliacao)
            <section class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm dark:border-emerald-900/50 dark:from-emerald-950/30 dark:to-gray-900">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Avaliação do solicitante</h3>
                        <p class="text-sm text-gray-500">Feedback após a finalização do chamado</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-white/80 p-4 ring-1 ring-emerald-100 dark:bg-gray-900/60 dark:ring-emerald-900/40">
                        <p class="text-sm text-gray-500">Satisfação geral</p>
                        <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $chamado->avaliacao->nota_satisfacao }}<span class="text-lg font-medium text-gray-400">/5</span></p>
                    </div>
                    <div class="rounded-xl bg-white/80 p-4 ring-1 ring-emerald-100 dark:bg-gray-900/60 dark:ring-emerald-900/40">
                        <p class="text-sm text-gray-500">Tempo de resolução</p>
                        <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $chamado->avaliacao->nota_tempo_resolucao }}<span class="text-lg font-medium text-gray-400">/5</span></p>
                    </div>
                </div>

                @if ($chamado->avaliacao->comentario)
                    <blockquote class="mt-4 rounded-xl border-s-4 border-emerald-400 bg-white/80 p-4 text-sm italic leading-relaxed text-gray-700 dark:bg-gray-900/60 dark:text-gray-200">
                        “{{ $chamado->avaliacao->comentario }}”
                    </blockquote>
                @endif
            </section>
        @endif
    </div>
</x-filament-panels::page>
