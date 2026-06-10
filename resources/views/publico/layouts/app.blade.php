<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#415A77">
    <title>@yield('titulo', 'Sistema de Chamados')</title>
    <script>
        (function () {
            try {
                var tema = localStorage.getItem('tema-publico');
                if (tema === 'dark' || (!tema && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="shell-body antialiased">
    <header class="shell-header sticky top-0 z-50 shrink-0 border-b">
        <div class="mx-auto max-w-5xl px-4 py-3 sm:px-6">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('home') }}"
                    class="titulo-pagina flex items-center gap-2.5 rounded-lg text-base outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-500 text-primary-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </span>
                    <span class="max-w-[9rem] truncate text-sm sm:max-w-none sm:text-base">Sistema de Chamados</span>
                </a>

                <div class="flex items-center gap-1 sm:gap-2">
                    <nav class="hidden items-center gap-1 sm:flex">
                        <a href="{{ route('home') }}"
                            @class([
                                'nav-link-publico',
                                'nav-link-publico-ativo' => request()->routeIs('home'),
                            ])>
                            Início
                        </a>
                        <a href="{{ route('chamados.criar') }}"
                            @class([
                                'nav-link-publico',
                                'nav-link-publico-ativo' => request()->routeIs('chamados.criar', 'chamados.salvar', 'chamados.sucesso'),
                            ])>
                            Abrir Chamado
                        </a>
                        <a href="{{ route('chamados.consultar') }}"
                            @class([
                                'nav-link-publico',
                                'nav-link-publico-ativo' => request()->routeIs('chamados.consultar*'),
                            ])>
                            Consultar Chamado
                        </a>
                    </nav>

                    <button type="button" id="btn-alternar-tema"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl texto-corpo outline-none transition hover:bg-primary-100 dark:hover:bg-primary-600 focus-visible:ring-2 focus-visible:ring-primary-500/40"
                        aria-label="Alternar tema">
                        <svg data-icone="claro" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg data-icone="escuro" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    <button type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl texto-corpo outline-none transition hover:bg-primary-100 dark:hover:bg-primary-600 focus-visible:ring-2 focus-visible:ring-primary-500/40 sm:hidden"
                        data-collapse-toggle="menu-mobile"
                        aria-controls="menu-mobile"
                        aria-expanded="false">
                        <span class="sr-only">Abrir menu</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <nav id="menu-mobile" class="mt-3 hidden flex-col gap-1 border-t pt-3 sm:hidden" style="border-color: var(--palette-border)">
                <a href="{{ route('home') }}"
                    @class([
                        'nav-link-publico',
                        'nav-link-publico-ativo' => request()->routeIs('home'),
                    ])>
                    Início
                </a>
                <a href="{{ route('chamados.criar') }}"
                    @class([
                        'nav-link-publico',
                        'nav-link-publico-ativo' => request()->routeIs('chamados.criar', 'chamados.salvar', 'chamados.sucesso'),
                    ])>
                    Abrir Chamado
                </a>
                <a href="{{ route('chamados.consultar') }}"
                    @class([
                        'nav-link-publico',
                        'nav-link-publico-ativo' => request()->routeIs('chamados.consultar*'),
                    ])>
                    Consultar Chamado
                </a>
            </nav>
        </div>
    </header>

    <main class="shell-main mx-auto w-full max-w-5xl px-4 py-4 sm:px-6 sm:py-5 @yield('classe_main')">
        @if (session('sucesso'))
            <div class="mb-6 rounded-xl border border-emerald-300/50 bg-emerald-50 p-4 text-emerald-900 dark:border-emerald-700/40 dark:bg-emerald-950/30 dark:text-emerald-200" role="alert">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <span class="font-medium">Sucesso!</span>
                        <p class="mt-1 text-sm">{{ session('sucesso') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('erro'))
            <div class="mb-6 rounded-xl border border-red-300/50 bg-red-50 p-4 text-red-900 dark:border-red-800/40 dark:bg-red-950/30 dark:text-red-200" role="alert">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <span class="font-medium">Atenção!</span>
                        <p class="mt-1 text-sm">{{ session('erro') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-300/50 bg-red-50 p-4 text-red-900 dark:border-red-800/40 dark:bg-red-950/30 dark:text-red-200" role="alert">
                <p class="mb-2 font-medium">Corrija os erros abaixo:</p>
                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('conteudo')
    </main>

    <footer class="shell-footer shrink-0 border-t px-4 py-4 text-center text-sm">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </footer>
</body>
</html>
