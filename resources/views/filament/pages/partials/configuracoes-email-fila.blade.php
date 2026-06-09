@php
    /** @var \App\Filament\Pages\Configuracoes $this */
    $info = $this->getInformacoesSistema();
    $fila = $this->getStatusFila();
@endphp

<div class="space-y-4">
    {{-- Status do worker --}}
    <div @class([
        'rounded-xl border p-4',
        'border-success-300 bg-success-50 dark:border-success-700 dark:bg-success-950/20' => $fila['rodando'],
        'border-danger-300 bg-danger-50 dark:border-danger-700 dark:bg-danger-950/20' => ! $fila['rodando'],
    ])>
        <div class="flex items-start gap-3">
            <span @class([
                'relative mt-0.5 flex h-3 w-3 shrink-0 rounded-full',
                'bg-success-500' => $fila['rodando'],
                'bg-danger-500' => ! $fila['rodando'],
            ])>
                @if ($fila['rodando'])
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success-400 opacity-75"></span>
                @endif
            </span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="font-semibold text-gray-950 dark:text-white">{{ $fila['rotulo'] }}</p>
                    <x-filament::badge :color="$fila['cor']">{{ $fila['driver'] }}</x-filament::badge>
                </div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $fila['mensagem'] }}</p>
                @if ($fila['ultimo_heartbeat'])
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Último sinal: {{ $fila['ultimo_heartbeat']->diffForHumans() }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Métricas da fila --}}
    @if ($fila['precisa_worker'])
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-xl bg-gray-50 p-3 text-center dark:bg-white/5">
                <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $fila['jobs_pendentes'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Jobs pendentes</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-3 text-center dark:bg-white/5">
                <p @class([
                    'text-2xl font-bold',
                    'text-danger-600 dark:text-danger-400' => $fila['jobs_falhos'] > 0,
                    'text-gray-950 dark:text-white' => $fila['jobs_falhos'] === 0,
                ])>{{ $fila['jobs_falhos'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Jobs com falha</p>
            </div>
        </div>
    @endif

    {{-- Configurações --}}
    <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Fila de processamento</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $info['fila_rotulo'] }}</p>
        </div>
        <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Driver de e-mail</p>
            <div class="mt-1">
                <x-filament::badge :color="$info['mailer_cor']">{{ $info['mailer_rotulo'] }}</x-filament::badge>
            </div>
        </div>
    </div>

    @if (! $fila['rodando'] && $fila['precisa_worker'])
        <div class="rounded-lg bg-gray-950 px-4 py-3 font-mono text-xs text-gray-100 dark:bg-gray-800">
            php artisan queue:work
        </div>
    @endif

    @if ($info['mailer_alerta'])
        <div class="flex gap-2 rounded-lg border border-warning-300 bg-warning-50 p-3 text-sm text-warning-800 dark:border-warning-700 dark:bg-warning-950/30 dark:text-warning-200">
            <x-filament::icon icon="heroicon-o-exclamation-triangle" class="mt-0.5 h-5 w-5 shrink-0" />
            <p><strong>Modo teste:</strong> e-mails gravados no log. Configure SMTP no <code>.env</code> para produção.</p>
        </div>
    @endif
</div>
