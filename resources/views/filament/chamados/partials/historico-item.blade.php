@php
    $iniciais = collect(explode(' ', $historico->tecnico->nome))
        ->filter()
        ->take(2)
        ->map(fn (string $parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
        ->implode('');
@endphp

<div class="relative flex gap-4 pb-8 last:pb-0">
    @unless ($loop->last)
        <span class="absolute start-[1.15rem] top-12 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
    @endunless

    <div class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white shadow-sm ring-4 ring-white dark:ring-gray-900">
        {{ $iniciais }}
    </div>

    <div class="min-w-0 flex-1 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 space-y-1">
                <p class="text-base font-semibold text-gray-950 dark:text-white">
                    {{ $historico->tecnico->nome }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $historico->tecnico->setor?->nome ?? 'Sem setor' }}
                    <span class="mx-1 text-gray-300">·</span>
                    Técnico responsável pelo registro
                </p>
            </div>
            <time class="shrink-0 text-sm font-medium text-gray-500 dark:text-gray-400" datetime="{{ $historico->created_at->toIso8601String() }}">
                {{ $historico->created_at->format('d/m/Y') }}
                <span class="block text-xs font-normal">{{ $historico->created_at->format('H:i') }}</span>
            </time>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2">
            <x-badge-status :status="$historico->status" />
            @if ($historico->visivel_solicitante)
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Visível ao solicitante
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    Registro interno
                </span>
            @endif
        </div>

        <div class="mt-4 rounded-lg bg-gray-50 p-4 text-sm leading-relaxed text-gray-700 dark:bg-gray-800/60 dark:text-gray-200">
            {{ $historico->descricao }}
        </div>
    </div>
</div>
