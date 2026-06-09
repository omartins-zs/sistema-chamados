@php
    use App\Filament\Resources\Usuarios\UsuarioResource;

    $links = [
        [
            'titulo' => 'Abrir Chamado',
            'descricao' => 'Formulário público de abertura',
            'url' => route('chamados.criar'),
            'externo' => true,
            'icone' => 'heroicon-o-plus-circle',
            'cor' => 'primary',
        ],
        [
            'titulo' => 'Consultar Chamado',
            'descricao' => 'Busca por protocolo',
            'url' => route('chamados.consultar'),
            'externo' => true,
            'icone' => 'heroicon-o-magnifying-glass',
            'cor' => 'info',
        ],
        [
            'titulo' => 'Gerenciar Técnicos',
            'descricao' => 'Usuários do painel admin',
            'url' => UsuarioResource::getUrl('index'),
            'externo' => false,
            'icone' => 'heroicon-o-users',
            'cor' => 'warning',
        ],
    ];

    $coresIcone = [
        'primary' => 'bg-primary-50 text-primary-600 ring-primary-100 dark:bg-primary-950/40 dark:text-primary-400 dark:ring-primary-900',
        'info' => 'bg-sky-50 text-sky-600 ring-sky-100 dark:bg-sky-950/40 dark:text-sky-400 dark:ring-sky-900',
        'warning' => 'bg-amber-50 text-amber-600 ring-amber-100 dark:bg-amber-950/40 dark:text-amber-400 dark:ring-amber-900',
    ];
@endphp

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($links as $link)
        <a href="{{ $link['url'] }}" @if ($link['externo']) target="_blank" @endif
           class="group flex flex-col rounded-xl border border-gray-200 bg-white p-4 transition hover:border-primary-300 hover:shadow-md dark:border-white/10 dark:bg-gray-900 dark:hover:border-primary-600">
            <div class="flex items-start justify-between gap-3">
                <span @class([
                    'flex h-11 w-11 items-center justify-center rounded-xl ring-1 ring-inset',
                    $coresIcone[$link['cor']],
                ])>
                    <x-filament::icon :icon="$link['icone']" class="h-5 w-5" />
                </span>
                @if ($link['externo'])
                    <x-filament::icon icon="heroicon-m-arrow-top-right-on-square"
                        class="h-4 w-4 text-gray-300 transition group-hover:text-primary-500" />
                @else
                    <x-filament::icon icon="heroicon-m-chevron-right"
                        class="h-4 w-4 text-gray-300 transition group-hover:text-primary-500" />
                @endif
            </div>
            <p class="mt-4 font-semibold text-gray-950 dark:text-white">{{ $link['titulo'] }}</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $link['descricao'] }}</p>
            <p class="mt-3 text-xs font-medium text-primary-600 dark:text-primary-400">
                {{ $link['externo'] ? 'Área pública' : 'Painel admin' }}
            </p>
        </a>
    @endforeach
</div>
