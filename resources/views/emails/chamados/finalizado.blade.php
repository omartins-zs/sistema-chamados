<x-mail::message>
# Chamado finalizado

Olá, **{{ $chamado->nome_solicitante }}**!

O chamado **{{ $chamado->protocolo }}** foi finalizado.

Gostaríamos muito de saber sua opinião sobre o atendimento recebido.

<x-mail::button :url="$urlAvaliacao">
Avaliar atendimento
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
