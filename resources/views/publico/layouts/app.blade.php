<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#00468a">
    <title>@yield('titulo', 'Sistema de Chamados')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <header class="sticky top-0 z-50 bg-primary-500 text-white shadow-md">
        <div class="mx-auto max-w-5xl px-4 py-3 sm:py-4">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('chamados.criar') }}" class="text-base font-semibold sm:text-lg">
                    Sistema de Chamados
                </a>

                {{-- Menu desktop --}}
                <nav class="hidden items-center gap-4 text-sm sm:flex">
                    <a href="{{ route('chamados.criar') }}" class="hover:underline">Abrir Chamado</a>
                    <a href="{{ route('chamados.consultar') }}" class="hover:underline">Consultar Chamado</a>
                </nav>

                {{-- Botão menu mobile (Flowbite) --}}
                <button type="button"
                    class="inline-flex items-center rounded-lg p-2 text-white hover:bg-white/10 sm:hidden"
                    data-collapse-toggle="menu-mobile"
                    aria-controls="menu-mobile"
                    aria-expanded="false">
                    <span class="sr-only">Abrir menu</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            {{-- Menu mobile --}}
            <nav id="menu-mobile" class="mt-3 hidden flex-col gap-2 border-t border-white/20 pt-3 sm:hidden">
                <a href="{{ route('chamados.criar') }}" class="rounded-lg px-3 py-2 hover:bg-white/10">Abrir Chamado</a>
                <a href="{{ route('chamados.consultar') }}" class="rounded-lg px-3 py-2 hover:bg-white/10">Consultar Chamado</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 sm:py-8">
        @if (session('sucesso'))
            <div class="mb-6 rounded-lg border border-green-300 bg-green-50 p-4 text-green-800" role="alert">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
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
            <div class="mb-6 rounded-lg border border-red-300 bg-red-50 p-4 text-red-800" role="alert">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
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
            <div class="mb-6 rounded-lg border border-red-300 bg-red-50 p-4 text-red-800" role="alert">
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

    <footer class="border-t border-slate-200 bg-white px-4 py-6 text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </footer>
</body>
</html>
