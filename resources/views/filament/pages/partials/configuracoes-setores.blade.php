@php
    /** @var \App\Filament\Pages\Configuracoes $this */
    $setores = $this->getSetoresListagem();

    $icones = ['heroicon-o-code-bracket', 'heroicon-o-server-stack', 'heroicon-o-wrench-screwdriver', 'heroicon-o-signal'];
@endphp

@if (count($setores) === 0)
    <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 px-6 py-12 text-center dark:border-gray-600">
        <x-filament::icon icon="heroicon-o-building-office-2" class="mb-3 h-10 w-10 text-gray-300" />
        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Nenhum setor cadastrado</p>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach ($setores as $setor)
            <div class="rounded-xl border border-gray-200 bg-white p-4 transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                        <x-filament::icon :icon="$icones[$loop->index % count($icones)]" class="h-5 w-5 text-gray-500" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="font-semibold text-gray-950 dark:text-white">{{ $setor->nome }}</h4>
                            @if ($setor->ativo)
                                <x-filament::badge color="success" size="sm">Ativo</x-filament::badge>
                            @else
                                <x-filament::badge color="gray" size="sm">Inativo</x-filament::badge>
                            @endif
                        </div>
                        @if ($setor->descricao)
                            <p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $setor->descricao }}</p>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4 dark:border-white/10">
                    <div class="rounded-lg bg-gray-50 px-3 py-2 text-center dark:bg-white/5">
                        <p class="text-lg font-bold text-gray-950 dark:text-white">{{ $setor->usuarios_count }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Técnicos</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 px-3 py-2 text-center dark:bg-white/5">
                        <p class="text-lg font-bold text-gray-950 dark:text-white">{{ $setor->chamados_count }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Chamados</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
