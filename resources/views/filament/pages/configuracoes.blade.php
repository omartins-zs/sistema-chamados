@php
    /** @var \App\Filament\Pages\Configuracoes $this */
    use App\Filament\Resources\Setores\SetorResource;
    use App\Filament\Resources\Usuarios\UsuarioResource;

    $resumo = $this->getResumo();
    $sistema = $this->getInformacoesSistema();
    $fila = $this->getStatusFila();
    $setores = $this->getSetoresListagem();

    $iconesSetor = ['heroicon-o-code-bracket', 'heroicon-o-server-stack', 'heroicon-o-wrench-screwdriver', 'heroicon-o-signal'];

    $metricas = [
        ['label' => 'Chamados', 'valor' => $resumo['chamados'], 'icone' => 'heroicon-o-ticket', 'cor' => 'slate'],
        ['label' => 'Técnicos', 'valor' => $resumo['tecnicos'], 'icone' => 'heroicon-o-users', 'cor' => 'sky'],
        ['label' => 'Setores', 'valor' => $resumo['setores'], 'icone' => 'heroicon-o-building-office-2', 'cor' => 'violet'],
        ['label' => 'Avaliações', 'valor' => $resumo['avaliacoes'], 'icone' => 'heroicon-o-star', 'cor' => 'emerald'],
    ];

    $coresMetrica = [
        'slate' => 'flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
        'primary' => 'flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
        'sky' => 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
        'violet' => 'bg-violet-100 text-violet-700 dark:bg-violet-950/50 dark:text-violet-300',
        'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
    ];

    $itensSistema = [
        ['label' => 'Nome do sistema', 'valor' => $sistema['nome']],
        ['label' => 'URL da aplicação', 'valor' => $sistema['url'], 'link' => $sistema['url']],
        ['label' => 'Idioma', 'valor' => $sistema['locale']],
        ['label' => 'Fuso horário', 'valor' => $sistema['timezone']],
        ['label' => 'Laravel', 'valor' => $sistema['laravel']],
        ['label' => 'PHP', 'valor' => $sistema['php']],
    ];

    $links = [
        ['titulo' => 'Abrir Chamado', 'descricao' => 'Formulário público', 'url' => route('chamados.criar'), 'externo' => true, 'icone' => 'heroicon-o-plus-circle'],
        ['titulo' => 'Consultar Chamado', 'descricao' => 'Busca por protocolo', 'url' => route('chamados.consultar'), 'externo' => true, 'icone' => 'heroicon-o-magnifying-glass'],
        ['titulo' => 'Gerenciar Técnicos', 'descricao' => 'Usuários do painel', 'url' => UsuarioResource::getUrl('index'), 'externo' => false, 'icone' => 'heroicon-o-users'],
    ];
@endphp

<div class="fi-configuracoes space-y-6">
        {{-- Hero --}}
        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Painel Administrativo</p>
                        <h2 class="mt-1 text-2xl font-semibold text-gray-950 sm:text-3xl dark:text-white">{{ $sistema['nome'] }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-2xl">
                            Visão geral do ambiente, filas, setores e atalhos do sistema de chamados.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset {{ $sistema['ambiente_cor'] }}">
                            {{ $sistema['ambiente_rotulo'] }}
                        </span>
                        @if ($sistema['debug'])
                            <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1.5 text-sm font-medium text-orange-800 ring-1 ring-inset ring-orange-300 dark:bg-orange-500/20 dark:text-orange-100 dark:ring-orange-300/40">
                                Debug ativo
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Métricas --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($metricas as $metrica)
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="flex items-center gap-3">
                        <span @class(['flex h-11 w-11 items-center justify-center rounded-xl', $coresMetrica[$metrica['cor']]])>
                            <x-filament::icon :icon="$metrica['icone']" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $metrica['label'] }}</p>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $metrica['valor'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sistema + E-mails --}}
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <x-filament::icon icon="heroicon-o-cpu-chip" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white text-lg">Informações do Sistema</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-sm">Ambiente, versões e parâmetros ativos</p>
                    </div>
                </div>

                <dl class="divide-y divide-gray-100 dark:divide-white/10">
                    @foreach ($itensSistema as $item)
                        <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <dt class="text-sm text-gray-500 dark:text-gray-400 text-sm">{{ $item['label'] }}</dt>
                            <dd class="text-right text-sm font-semibold text-gray-950 dark:text-white">
                                @if (! empty($item['link']))
                                    <a href="{{ $item['link'] }}" target="_blank" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">
                                        {{ $item['valor'] }}
                                    </a>
                                @else
                                    {{ $item['valor'] }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                        <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white text-lg">E-mails e Filas</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-sm">Worker, processamento e notificações</p>
                    </div>
                </div>

                <div @class([
                    'mb-4 rounded-xl border p-4',
                    'border-success-300 bg-success-50 dark:border-success-700 dark:bg-success-950/20' => $fila['rodando'],
                    'border-danger-300 bg-danger-50 dark:border-danger-700 dark:bg-danger-950/20' => ! $fila['rodando'],
                ])>
                    <div class="flex items-start gap-3">
                        <span @class([
                            'relative mt-1 flex h-3 w-3 shrink-0 rounded-full',
                            'bg-success-500' => $fila['rodando'],
                            'bg-danger-500' => ! $fila['rodando'],
                        ])>
                            @if ($fila['rodando'])
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success-400 opacity-75"></span>
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-950 dark:text-white">{{ $fila['rotulo'] }}</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $fila['mensagem'] }}</p>
                            @if ($fila['ultimo_heartbeat'])
                                <p class="mt-2 text-xs text-gray-500">Último sinal: {{ $fila['ultimo_heartbeat']->diffForHumans() }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($fila['precisa_worker'])
                    <div class="mb-4 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-gray-50 px-3 py-3 text-center dark:bg-white/5">
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $fila['jobs_pendentes'] }}</p>
                            <p class="text-xs text-gray-500">Jobs pendentes</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-3 py-3 text-center dark:bg-white/5">
                            <p @class([
                                'text-2xl font-bold',
                                'text-danger-600' => $fila['jobs_falhos'] > 0,
                                'text-gray-950 dark:text-white' => $fila['jobs_falhos'] === 0,
                            ])>{{ $fila['jobs_falhos'] }}</p>
                            <p class="text-xs text-gray-500">Jobs com falha</p>
                        </div>
                    </div>
                @endif

                <dl class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                        <dt class="text-xs text-gray-500">Fila de processamento</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $sistema['fila_rotulo'] }}</dd>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                        <dt class="text-xs text-gray-500">Driver de e-mail</dt>
                        <dd class="mt-1">
                            <x-filament::badge :color="$sistema['mailer_cor']">{{ $sistema['mailer_rotulo'] }}</x-filament::badge>
                        </dd>
                    </div>
                </dl>

                @if (! $fila['rodando'] && $fila['precisa_worker'])
                    <div class="mt-4 rounded-lg bg-gray-950 px-4 py-3 font-mono text-xs text-gray-100">
                        php artisan queue:work
                    </div>
                @endif

                @if ($sistema['mailer_alerta'])
                    <div class="mt-4 flex gap-2 rounded-lg border border-warning-300 bg-warning-50 p-3 text-sm text-warning-800 dark:border-warning-700 dark:bg-warning-950/30 dark:text-warning-200">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="mt-0.5 h-5 w-5 shrink-0" />
                        <p><strong>Modo teste:</strong> e-mails gravados no log. Configure SMTP no <code class="rounded bg-warning-100 px-1 dark:bg-warning-900/50">.env</code> para produção.</p>
                    </div>
                @endif
            </section>
        </div>

        {{-- Protocolo --}}
        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <x-filament::icon icon="heroicon-o-hashtag" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white text-lg">Formato do Protocolo</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 text-sm">Identificador único gerado ao abrir um chamado</p>
                    </div>
                </div>
                <p class="rounded-xl bg-gray-50 px-5 py-3 font-mono text-lg font-bold tracking-wider text-primary-600 dark:bg-white/5 dark:text-primary-400">
                    CHM-2026-000001
                </p>
            </div>
            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                Padrão <strong>CHM-AAAA-NNNNNN</strong> — prefixo fixo, ano de abertura e sequência numérica com 6 dígitos.
            </p>
        </section>

        {{-- Setores --}}
        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">
                        <x-filament::icon icon="heroicon-o-building-office-2" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white text-lg">Setores Cadastrados</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-sm">Áreas de atendimento com técnicos e chamados</p>
                    </div>
                </div>
                <a href="{{ SetorResource::getUrl('index') }}"
                   class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">
                    Gerenciar setores
                    <x-filament::icon icon="heroicon-m-chevron-right" class="h-4 w-4" />
                </a>
            </div>

            @if (count($setores) === 0)
                <div class="rounded-xl border border-dashed border-gray-300 px-6 py-12 text-center dark:border-gray-600">
                    <x-filament::icon icon="heroicon-o-building-office-2" class="mx-auto mb-3 h-10 w-10 text-gray-300" />
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-sm">Nenhum setor cadastrado.</p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($setores as $setor)
                        <div class="rounded-xl border border-gray-200 p-4 transition hover:border-slate-400 hover:shadow-md dark:border-gray-700 dark:hover:border-slate-500">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50 dark:bg-white/5">
                                        <x-filament::icon :icon="$iconesSetor[$loop->index % count($iconesSetor)]" class="h-5 w-5 text-gray-500" />
                                    </span>
                                    <div class="min-w-0">
                                        <h4 class="font-semibold text-gray-950 dark:text-white">{{ $setor->nome }}</h4>
                                        @if ($setor->descricao)
                                            <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ $setor->descricao }}</p>
                                        @endif
                                    </div>
                                </div>
                                <x-filament::badge :color="$setor->ativo ? 'success' : 'gray'" size="sm">
                                    {{ $setor->ativo ? 'Ativo' : 'Inativo' }}
                                </x-filament::badge>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-950 dark:text-white">{{ $setor->usuarios_count }}</p>
                                    <p class="text-xs text-gray-500">Técnicos</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-950 dark:text-white">{{ $setor->chamados_count }}</p>
                                    <p class="text-xs text-gray-500">Chamados</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Acesso rápido --}}
        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="mb-4 text-lg font-semibold text-gray-950 dark:text-white">Acesso Rápido</h3>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($links as $link)
                    <a href="{{ $link['url'] }}" @if ($link['externo']) target="_blank" @endif
                       class="group flex items-center gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-slate-400 hover:bg-slate-50 dark:border-gray-700 dark:hover:border-slate-500 dark:hover:bg-slate-800/50">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            <x-filament::icon :icon="$link['icone']" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-950 dark:text-white">{{ $link['titulo'] }}</p>
                            <p class="text-xs text-gray-500">{{ $link['descricao'] }}</p>
                        </div>
                        <x-filament::icon
                            :icon="$link['externo'] ? 'heroicon-m-arrow-top-right-on-square' : 'heroicon-m-chevron-right'"
                            class="h-4 w-4 text-gray-300 transition group-hover:text-primary-500"
                        />
                    </a>
                @endforeach
            </div>
        </section>
</div>
