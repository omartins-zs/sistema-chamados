@extends('publico.layouts.app')

@section('titulo', 'Abrir Chamado')

@section('classe_main')
shell-main-centro pagina-abrir-chamado
@endsection

@section('conteudo')
<div class="mx-auto w-full max-w-4xl">
    <form action="{{ route('chamados.salvar') }}" method="POST" class="card-publico card-publico-compacto card-formulario-chamado">
        @csrf

        <header class="formulario-chamado-cabecalho">
            <p class="eyebrow-publico">Nova solicitação</p>
            <h1 class="titulo-pagina mt-0.5 text-xl sm:text-2xl">Abrir Chamado</h1>
            <p class="texto-corpo mt-1 text-xs sm:text-sm">
                Preencha os dados abaixo. Você receberá um protocolo para acompanhar o atendimento.
            </p>
        </header>

        <div class="formulario-chamado-corpo">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="nome_solicitante" class="label-publico">Nome do Solicitante</label>
                    <input type="text" id="nome_solicitante" name="nome_solicitante" value="{{ old('nome_solicitante') }}"
                        class="input-publico" required autocomplete="name">
                </div>
                <div>
                    <label for="email_solicitante" class="label-publico">E-mail</label>
                    <input type="email" id="email_solicitante" name="email_solicitante" value="{{ old('email_solicitante') }}"
                        class="input-publico" required autocomplete="email">
                </div>
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="telefone_solicitante" class="label-publico">Telefone / WhatsApp</label>
                    <input type="text" id="telefone_solicitante" name="telefone_solicitante" value="{{ old('telefone_solicitante') }}"
                        class="input-publico" required autocomplete="tel">
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="complexidade" class="label-publico">Complexidade</label>
                    <select id="complexidade" name="complexidade" class="input-publico" required>
                        <option value="">Selecione...</option>
                        @foreach (\App\Enums\ComplexidadeChamadoEnum::opcoes() as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(old('complexidade') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="setor_id" class="label-publico">Setor Responsável</label>
                    <select id="setor_id" name="setor_id" class="input-publico" required>
                        <option value="">Selecione...</option>
                        @foreach ($setores as $setor)
                            <option value="{{ $setor->id }}" @selected(old('setor_id') == $setor->id)>{{ $setor->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="titulo" class="label-publico">Título do Chamado</label>
                <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}"
                    class="input-publico" required placeholder="Ex.: Impressora não imprime">
            </div>

            <div>
                <label for="descricao" class="label-publico">Descrição Detalhada</label>
                <textarea id="descricao" name="descricao" rows="2"
                    class="input-publico input-publico-textarea" required placeholder="Descreva o problema com o máximo de detalhes possível.">{{ old('descricao') }}</textarea>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Mínimo de 10 caracteres.</p>
            </div>
        </div>

        <div class="formulario-chamado-acoes flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <a href="{{ route('chamados.consultar') }}" class="btn-secundario w-full sm:w-auto">
                Consultar protocolo
            </a>
            <button type="submit" data-testid="btn-abrir-chamado" class="btn-primario w-full sm:w-auto">
                Abrir Chamado
            </button>
        </div>
    </form>
</div>
@endsection
