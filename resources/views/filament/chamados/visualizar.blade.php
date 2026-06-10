@php
    $totalHistoricos = $historicos->count();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="border-s-4 border-primary-500 px-6 py-5 dark:border-primary-400">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Descrição</p>
                <p class="mt-2 text-base leading-relaxed text-gray-950 dark:text-white">{{ $chamado->descricao }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-3 px-6 py-4">
                <x-badge-status :status="$chamado->status" tamanho="md" />
                <x-badge-complexidade :complexidade="$chamado->complexidade" tamanho="md" />
            </div>

            <dl class="grid border-t border-gray-200 sm:grid-cols-2 lg:grid-cols-4 dark:border-white/10">
                <div class="border-b border-gray-200 px-6 py-4 sm:border-e lg:border-b-0 dark:border-white/10">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Setor</dt>
                    <dd class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $chamado->setor->nome }}</dd>
                </div>
                <div class="border-b border-gray-200 px-6 py-4 lg:border-e lg:border-b-0 dark:border-white/10">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Abertura</dt>
                    <dd class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $chamado->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="border-b border-gray-200 px-6 py-4 sm:border-e sm:border-b-0 dark:border-white/10">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Finalização</dt>
                    <dd class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $chamado->finalizado_em?->format('d/m/Y H:i') ?? 'Em andamento' }}</dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Técnico</dt>
                    <dd class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $chamado->tecnicoResponsavel?->nome ?? 'Aguardando atribuição' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">Solicitante</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Dados de contato para retorno</p>

            <dl class="mt-4 divide-y divide-gray-100 dark:divide-white/10">
                <div class="flex items-center justify-between gap-4 py-3 first:pt-0">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Nome</dt>
                    <dd class="text-sm font-semibold text-gray-950 dark:text-white">{{ $chamado->nome_solicitante }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">E-mail</dt>
                    <dd>
                        <a href="mailto:{{ $chamado->email_solicitante }}" class="text-sm font-semibold text-primary-600 hover:underline dark:text-primary-400">{{ $chamado->email_solicitante }}</a>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3 last:pb-0">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Telefone</dt>
                    <dd class="text-sm font-semibold text-gray-950 dark:text-white">{{ $chamado->telefone_solicitante }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="mb-5 flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">Linha do tempo</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Histórico completo de interações</p>
                </div>
                <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                    {{ $totalHistoricos }} {{ $totalHistoricos === 1 ? 'registro' : 'registros' }}
                </span>
            </div>

            @forelse ($historicos as $historico)
                @include('filament.chamados.partials.historico-item', ['historico' => $historico])
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center dark:border-gray-600">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhum histórico registrado ainda</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Use <strong>Adicionar Histórico</strong> para registrar a primeira interação.</p>
                </div>
            @endforelse
        </section>

        @if ($chamado->avaliacao)
            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Avaliação do solicitante</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Feedback após a finalização</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Satisfação geral</p>
                        <p class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">{{ $chamado->avaliacao->nota_satisfacao }}<span class="text-base font-medium text-gray-400">/5</span></p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tempo de resolução</p>
                        <p class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">{{ $chamado->avaliacao->nota_tempo_resolucao }}<span class="text-base font-medium text-gray-400">/5</span></p>
                    </div>
                </div>

                @if ($chamado->avaliacao->comentario)
                    <blockquote class="mt-4 rounded-lg bg-gray-50 p-4 text-sm italic text-gray-700 dark:bg-white/5 dark:text-gray-300">
                        “{{ $chamado->avaliacao->comentario }}”
                    </blockquote>
                @endif
            </section>
        @endif
    </div>
</x-filament-panels::page>
