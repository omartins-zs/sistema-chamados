@extends('publico.layouts.app')

@section('titulo', 'Situação do Chamado — '.$chamado->protocolo)

@section('conteudo')
<div class="mx-auto w-full max-w-4xl space-y-6">
    <section class="card-publico">
        <p class="eyebrow-publico">Consulta de protocolo</p>
        <p class="texto-protocolo mt-2 text-lg sm:text-xl" data-testid="protocolo-chamado">{{ $chamado->protocolo }}</p>
        <h1 class="titulo-pagina mt-3 text-2xl leading-tight sm:text-3xl">{{ $chamado->titulo }}</h1>

        <div class="mt-5 flex flex-wrap items-center gap-3">
            <x-badge-status :status="$chamado->status" tamanho="md" />
            <x-badge-complexidade :complexidade="$chamado->complexidade" tamanho="md" />
        </div>
    </section>

    <section class="card-publico">
        <h2 class="titulo-pagina text-lg">Situação Atual do Chamado</h2>
        <p class="texto-corpo mt-1 text-sm">Informações atualizadas do atendimento.</p>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="info-item-publico">
                <dt>Solicitante</dt>
                <dd>{{ $chamado->nome_solicitante }}</dd>
            </div>
            <div class="info-item-publico">
                <dt>Setor Responsável</dt>
                <dd>{{ $chamado->setor->nome }}</dd>
            </div>
            <div class="info-item-publico">
                <dt>Técnico Responsável</dt>
                <dd>{{ $chamado->tecnicoResponsavel?->nome ?? 'Aguardando atribuição' }}</dd>
            </div>
            <div class="info-item-publico">
                <dt>Data de Abertura</dt>
                <dd>{{ $chamado->created_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </section>

    @if ($chamado->historicosPublicos->isNotEmpty())
        <section class="card-publico">
            <div class="mb-6 flex flex-col gap-2 border-b pb-4 sm:flex-row sm:items-center sm:justify-between" style="border-color: var(--palette-border)">
                <div>
                    <h2 class="titulo-pagina text-lg">Histórico do Chamado</h2>
                    <p class="texto-corpo mt-1 text-sm">Atualizações visíveis ao solicitante.</p>
                </div>
                <span class="contador-publico">
                    {{ $chamado->historicosPublicos->count() }} {{ $chamado->historicosPublicos->count() === 1 ? 'registro' : 'registros' }}
                </span>
            </div>

            <div class="space-y-0">
                @foreach ($chamado->historicosPublicos as $historico)
                    @php
                        $iniciais = collect(explode(' ', $historico->tecnico->nome))
                            ->filter()
                            ->take(2)
                            ->map(fn (string $parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
                            ->implode('');
                    @endphp
                    <div class="relative flex gap-4 pb-8 last:pb-0">
                        @unless ($loop->last)
                            <span class="absolute start-[1.05rem] top-11 bottom-0 w-px" style="background-color: var(--palette-border)" aria-hidden="true"></span>
                        @endunless

                        <div class="avatar-publico relative z-10 text-sm">
                            {{ $iniciais }}
                        </div>

                        <article class="historico-card-publico min-w-0 flex-1">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="titulo-pagina font-semibold">{{ $historico->tecnico->nome }}</p>
                                    <p class="texto-corpo text-xs">{{ $historico->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <x-badge-status :status="$historico->status" tamanho="sm" />
                            </div>
                            <p class="historico-texto-publico">{{ $historico->descricao }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
        <a href="{{ route('chamados.consultar') }}" class="btn-secundario w-full sm:w-auto">Nova consulta</a>
        <a href="{{ route('chamados.criar') }}" class="btn-primario w-full sm:w-auto">Abrir novo chamado</a>
    </div>
</div>
@endsection
