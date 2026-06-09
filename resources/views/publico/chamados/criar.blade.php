@extends('publico.layouts.app')

@section('titulo', 'Abrir Chamado')

@section('conteudo')
<div class="mx-auto w-full max-w-3xl">
    <div class="mb-6 text-center sm:mb-8">
        <h1 class="text-2xl font-bold text-primary-600 sm:text-3xl">Abrir Chamado</h1>
        <p class="mt-2 text-sm text-slate-600 sm:text-base">Preencha o formulário abaixo para registrar sua solicitação.</p>
    </div>

    <form action="{{ route('chamados.salvar') }}" method="POST" class="space-y-5 rounded-2xl bg-white p-4 shadow-lg sm:space-y-6 sm:p-8">
        @csrf

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="nome_solicitante" class="mb-2 block text-sm font-medium text-slate-700">Nome do Solicitante</label>
                <input type="text" id="nome_solicitante" name="nome_solicitante" value="{{ old('nome_solicitante') }}"
                    class="block w-full rounded-lg border border-slate-300 p-2.5 focus:border-primary-500 focus:ring-primary-500" required>
            </div>
            <div>
                <label for="email_solicitante" class="mb-2 block text-sm font-medium text-slate-700">E-mail</label>
                <input type="email" id="email_solicitante" name="email_solicitante" value="{{ old('email_solicitante') }}"
                    class="block w-full rounded-lg border border-slate-300 p-2.5 focus:border-primary-500 focus:ring-primary-500" required>
            </div>
        </div>

        <div>
            <label for="telefone_solicitante" class="mb-2 block text-sm font-medium text-slate-700">Telefone / WhatsApp</label>
            <input type="text" id="telefone_solicitante" name="telefone_solicitante" value="{{ old('telefone_solicitante') }}"
                class="block w-full rounded-lg border border-slate-300 p-2.5 focus:border-primary-500 focus:ring-primary-500" required>
        </div>

        <div>
            <label for="titulo" class="mb-2 block text-sm font-medium text-slate-700">Título do Chamado</label>
            <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}"
                class="block w-full rounded-lg border border-slate-300 p-2.5 focus:border-primary-500 focus:ring-primary-500" required>
        </div>

        <div>
            <label for="descricao" class="mb-2 block text-sm font-medium text-slate-700">Descrição Detalhada</label>
            <textarea id="descricao" name="descricao" rows="5"
                class="block w-full rounded-lg border border-slate-300 p-2.5 focus:border-primary-500 focus:ring-primary-500" required>{{ old('descricao') }}</textarea>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="complexidade" class="mb-2 block text-sm font-medium text-slate-700">Complexidade</label>
                <select id="complexidade" name="complexidade"
                    class="block w-full rounded-lg border border-slate-300 p-2.5 focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">Selecione...</option>
                    @foreach (\App\Enums\ComplexidadeChamadoEnum::opcoes() as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(old('complexidade') === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="setor_id" class="mb-2 block text-sm font-medium text-slate-700">Setor Responsável</label>
                <select id="setor_id" name="setor_id"
                    class="block w-full rounded-lg border border-slate-300 p-2.5 focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">Selecione...</option>
                    @foreach ($setores as $setor)
                        <option value="{{ $setor->id }}" @selected(old('setor_id') == $setor->id)>{{ $setor->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 pt-4 sm:flex-row sm:justify-end">
            <button type="submit" data-testid="btn-abrir-chamado" class="w-full rounded-lg bg-primary-500 px-6 py-3 font-medium text-white hover:bg-primary-600 focus:ring-4 focus:ring-primary-300 sm:w-auto">
                Abrir Chamado
            </button>
        </div>
    </form>
</div>
@endsection
