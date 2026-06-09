@php
    /** @var \App\Filament\Pages\Configuracoes $this */
    $info = $this->getInformacoesSistema();

    $itens = [
        ['icone' => 'heroicon-o-building-office-2', 'label' => 'Nome do sistema', 'valor' => $info['nome']],
        ['icone' => 'heroicon-o-globe-alt', 'label' => 'URL', 'valor' => $info['url'], 'link' => $info['url']],
        ['icone' => 'heroicon-o-language', 'label' => 'Idioma', 'valor' => $info['locale']],
        ['icone' => 'heroicon-o-clock', 'label' => 'Fuso horário', 'valor' => $info['timezone']],
        ['icone' => 'heroicon-o-code-bracket', 'label' => 'Laravel', 'valor' => $info['laravel']],
        ['icone' => 'heroicon-o-command-line', 'label' => 'PHP', 'valor' => $info['php']],
    ];
@endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center gap-2">
        <x-filament::badge :color="$info['ambiente_cor']">{{ $info['ambiente_rotulo'] }}</x-filament::badge>
        @if ($info['debug'])
            <x-filament::badge color="warning">Debug ativo</x-filament::badge>
        @endif
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        @foreach ($itens as $item)
            <div class="flex items-start gap-3 rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <x-filament::icon :icon="$item['icone']" class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $item['label'] }}</p>
                    @if (! empty($item['link']))
                        <a href="{{ $item['link'] }}" target="_blank" class="mt-0.5 block truncate text-sm font-semibold text-primary-600 hover:underline dark:text-primary-400">
                            {{ $item['valor'] }}
                        </a>
                    @else
                        <p class="mt-0.5 truncate text-sm font-semibold text-gray-950 dark:text-white">{{ $item['valor'] }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50/50 p-4 dark:border-gray-600 dark:bg-white/5">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-o-hashtag" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
            <p class="text-sm font-semibold text-gray-950 dark:text-white">Protocolo de chamados</p>
        </div>
        <p class="mt-2 font-mono text-lg font-bold tracking-wide text-primary-600 dark:text-primary-400">CHM-2026-000001</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Padrão CHM-AAAA-NNNNNN — prefixo, ano e sequência de 6 dígitos.</p>
    </div>
</div>
