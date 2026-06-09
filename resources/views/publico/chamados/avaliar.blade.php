@extends('publico.layouts.app')

@section('titulo', 'Avaliar Atendimento')

@section('conteudo')
<div class="mx-auto max-w-2xl">
    @if (session('sucesso'))
        <div class="rounded-2xl bg-white p-8 text-center shadow-lg">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-primary-600">Obrigado!</h1>
            <p class="mt-2 text-slate-600">{{ session('sucesso') }}</p>
            <a href="{{ route('chamados.finalizado', $chamado->protocolo) }}" class="mt-6 inline-block text-primary-600 hover:underline">
                Ver chamado finalizado
            </a>
        </div>
    @else
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-primary-600">Avaliar Atendimento</h1>
            <p class="mt-2 text-slate-600">Chamado <strong>{{ $chamado->protocolo }}</strong> — {{ $chamado->titulo }}</p>
        </div>

        <form action="{{ route('chamados.avaliar.salvar', ['protocolo' => $chamado->protocolo, 'token' => $token]) }}" method="POST"
            class="space-y-6 rounded-2xl bg-white p-4 shadow-lg sm:p-8">
            @csrf

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Satisfação com o atendimento (1 a 5)</label>
                <div class="flex flex-wrap justify-center gap-2 sm:justify-start">
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="flex cursor-pointer flex-col items-center">
                            <input type="radio" name="nota_satisfacao" value="{{ $i }}" class="peer sr-only" @checked(old('nota_satisfacao') == $i) required>
                            <span class="flex h-11 w-11 items-center justify-center rounded-full border-2 border-slate-300 text-sm font-medium peer-checked:border-primary-500 peer-checked:bg-primary-500 peer-checked:text-white sm:h-10 sm:w-10">{{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Tempo de resolução (1 a 5)</label>
                <div class="flex flex-wrap justify-center gap-2 sm:justify-start">
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="flex cursor-pointer flex-col items-center">
                            <input type="radio" name="nota_tempo_resolucao" value="{{ $i }}" class="peer sr-only" @checked(old('nota_tempo_resolucao') == $i) required>
                            <span class="flex h-11 w-11 items-center justify-center rounded-full border-2 border-slate-300 text-sm font-medium peer-checked:border-primary-500 peer-checked:bg-primary-500 peer-checked:text-white sm:h-10 sm:w-10">{{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <div>
                <label for="comentario" class="mb-2 block text-sm font-medium text-slate-700">Comentário (opcional)</label>
                <textarea id="comentario" name="comentario" rows="4"
                    class="block w-full rounded-lg border border-slate-300 p-2.5 focus:border-primary-500 focus:ring-primary-500">{{ old('comentario') }}</textarea>
            </div>

            <button type="submit" class="w-full rounded-lg bg-primary-500 px-6 py-3 font-medium text-white hover:bg-primary-600">
                Enviar Avaliação
            </button>
        </form>
    @endif
</div>
@endsection
