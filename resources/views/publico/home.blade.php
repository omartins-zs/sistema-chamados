@extends('publico.layouts.app')

@section('titulo', 'Sistema de Chamados Técnicos')

@section('classe_main')
shell-main-centro
@endsection

@section('conteudo')
<section class="card-publico card-publico-compacto relative overflow-hidden">
    <div class="relative z-10 mx-auto max-w-3xl text-center">
        <p class="eyebrow-publico">Central de atendimento</p>
        <h1 class="titulo-pagina mt-2 text-3xl sm:text-4xl">Sistema de Chamados Técnicos</h1>
        <p class="texto-corpo mx-auto mt-3 max-w-2xl text-sm sm:text-base">
            Abra solicitações, acompanhe o andamento pelo protocolo e avalie o atendimento quando o chamado for finalizado.
        </p>
        <div class="mt-5 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ route('chamados.criar') }}" data-testid="btn-hero-abrir-chamado" class="btn-primario w-full sm:w-auto">
                Abrir Chamado
            </a>
            <a href="{{ route('chamados.consultar') }}" data-testid="btn-hero-consultar-chamado" class="btn-secundario w-full sm:w-auto">
                Consultar Chamado
            </a>
        </div>

        <ol class="mt-5 grid gap-3 border-t pt-5 text-left sm:grid-cols-3" style="border-color: var(--palette-border)">
            <li class="passo-card passo-card-compacto">
                <span class="passo-numero">1. Abra</span>
                <p class="texto-corpo mt-1 text-xs sm:text-sm">Descreva o problema e selecione o setor.</p>
            </li>
            <li class="passo-card passo-card-compacto">
                <span class="passo-numero">2. Acompanhe</span>
                <p class="texto-corpo mt-1 text-xs sm:text-sm">Consulte atualizações pelo protocolo.</p>
            </li>
            <li class="passo-card passo-card-compacto">
                <span class="passo-numero">3. Avalie</span>
                <p class="texto-corpo mt-1 text-xs sm:text-sm">Envie sua avaliação após a finalização.</p>
            </li>
        </ol>
    </div>
</section>

<section class="mt-5 grid gap-4 sm:grid-cols-3">
    <article class="card-publico card-publico-compacto">
        <div class="icone-caixa-accento icone-caixa-compacto mb-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
        </div>
        <h2 class="titulo-pagina text-base sm:text-lg">Abertura simples</h2>
        <p class="texto-corpo mt-1.5 text-xs sm:text-sm">
            Formulário objetivo para registrar o problema, escolher o setor e receber o protocolo na hora.
        </p>
    </article>

    <article class="card-publico card-publico-compacto">
        <div class="icone-caixa-accento icone-caixa-compacto mb-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h2 class="titulo-pagina text-base sm:text-lg">Acompanhamento</h2>
        <p class="texto-corpo mt-1.5 text-xs sm:text-sm">
            Consulte a situação atual e o histórico público do chamado usando apenas o número do protocolo.
        </p>
    </article>

    <article class="card-publico card-publico-compacto">
        <div class="icone-caixa-accento icone-caixa-compacto mb-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </div>
        <h2 class="titulo-pagina text-base sm:text-lg">Avaliação</h2>
        <p class="texto-corpo mt-1.5 text-xs sm:text-sm">
            Após a finalização, avalie a satisfação e o tempo de resolução para melhorar o atendimento.
        </p>
    </article>
</section>
@endsection
