<x-mail::message>
# Chamado registrado com sucesso

Olá, **{{ $chamado->nome_solicitante }}**!

Seu chamado foi registrado em nosso sistema.

**Protocolo:** {{ $chamado->protocolo }}

**Título:** {{ $chamado->titulo }}

**Status:** {{ $chamado->status->rotulo() }}

<x-mail::button :url="$urlSucesso">
Acompanhar chamado
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
