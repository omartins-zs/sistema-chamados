@php
    $iniciais = collect(explode(' ', $historico->tecnico->nome))
        ->filter()
        ->take(2)
        ->map(fn (string $parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
        ->implode('');
@endphp

<div class="relative flex gap-4 pb-7 last:pb-0">
    @unless ($loop->last)
        <span class="absolute start-[1.05rem] top-11 bottom-0 w-px bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
    @endunless

    <div class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-700 text-sm font-bold text-white ring-4 ring-white dark:bg-gray-600 dark:ring-gray-900">
        {{ $iniciais }}
    </div>

    <div class="min-w-0 flex-1">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="font-semibold text-gray-950 dark:text-white">{{ $historico->tecnico->nome }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $historico->tecnico->setor?->nome ?? 'Sem setor' }}</p>
            </div>
            <time class="shrink-0 text-sm text-gray-500 dark:text-gray-400" datetime="{{ $historico->created_at->toIso8601String() }}">
                {{ $historico->created_at->format('d/m/Y H:i') }}
            </time>
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-2">
            <x-badge-status :status="$historico->status" />
            @if ($historico->visivel_solicitante)
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-800 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-200 dark:ring-emerald-800">
                    Visível ao solicitante
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                    Registro interno
                </span>
            @endif
        </div>

        <p class="mt-3 rounded-lg bg-gray-50 p-3 text-sm leading-relaxed text-gray-700 dark:bg-white/5 dark:text-gray-300">
            {{ $historico->descricao }}
        </p>
    </div>
</div>
