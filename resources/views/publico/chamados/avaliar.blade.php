@extends('publico.layouts.app')

@section('titulo', 'Avaliar Atendimento')

@section('conteudo')
<div class="mx-auto max-w-2xl">
    @if (session('sucesso'))
        <div class="card-publico text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="titulo-pagina text-2xl">Obrigado!</h1>
            <p class="texto-corpo mt-2">{{ session('sucesso') }}</p>
            <a href="{{ route('chamados.finalizado', $chamado->protocolo) }}" class="link-paleta mt-6 inline-block">
                Ver chamado finalizado
            </a>
        </div>
    @else
        <div class="mb-8 text-center">
            <h1 class="titulo-pagina text-3xl">Avaliar Atendimento</h1>
            <p class="texto-corpo mt-2">Chamado <strong class="texto-protocolo">{{ $chamado->protocolo }}</strong> — {{ $chamado->titulo }}</p>
        </div>

        <form action="{{ route('chamados.avaliar.salvar', ['protocolo' => $chamado->protocolo, 'token' => $token]) }}" method="POST" class="card-publico space-y-6">
            @csrf

            <div>
                <label class="label-publico">Satisfação com o atendimento (1 a 5)</label>
                <div class="flex flex-wrap justify-center gap-2 sm:justify-start">
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="flex cursor-pointer flex-col items-center">
                            <input type="radio" name="nota_satisfacao" value="{{ $i }}" class="peer sr-only" @checked(old('nota_satisfacao') == $i) required>
                            <span class="nota-avaliacao">{{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <div>
                <label class="label-publico">Tempo de resolução (1 a 5)</label>
                <div class="flex flex-wrap justify-center gap-2 sm:justify-start">
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="flex cursor-pointer flex-col items-center">
                            <input type="radio" name="nota_tempo_resolucao" value="{{ $i }}" class="peer sr-only" @checked(old('nota_tempo_resolucao') == $i) required>
                            <span class="nota-avaliacao">{{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <div>
                <label for="comentario" class="label-publico">Comentário (opcional)</label>
                <textarea id="comentario" name="comentario" rows="4" class="input-publico">{{ old('comentario') }}</textarea>
            </div>

            <button type="submit" class="btn-primario w-full">
                Enviar Avaliação
            </button>
        </form>
    @endif
</div>
@endsection
