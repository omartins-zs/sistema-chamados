@props([
    'complexidade',
    'tamanho' => 'sm',
])

@php
    use App\Enums\ComplexidadeChamadoEnum;

    $complexidade = ComplexidadeChamadoEnum::normalizar($complexidade);

    $tamanhos = [
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base font-bold',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full font-semibold ring-1 ring-inset',
    $complexidade->classesBadge(),
    $tamanhos[$tamanho] ?? $tamanhos['sm'],
]) }}>
    {{ $complexidade->rotulo() }}
</span>
