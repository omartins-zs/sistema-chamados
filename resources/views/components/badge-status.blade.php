@props([
    'status',
    'tamanho' => 'sm',
])

@php
    use App\Enums\StatusChamadoEnum;

    $status = StatusChamadoEnum::normalizar($status);

    $tamanhos = [
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base font-bold',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full font-semibold ring-1 ring-inset',
    $status->classesBadge(),
    $tamanhos[$tamanho] ?? $tamanhos['sm'],
]) }}>
    <span class="h-2 w-2 shrink-0 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    {{ $status->rotulo() }}
</span>
