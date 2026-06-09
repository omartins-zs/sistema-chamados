<?php

namespace App\Enums;

enum ComplexidadeChamadoEnum: string
{
    case BAIXA = 'baixa';
    case MEDIA = 'media';
    case ALTA = 'alta';
    case CRITICA = 'critica';

    public function rotulo(): string
    {
        return match ($this) {
            self::BAIXA => 'Baixa',
            self::MEDIA => 'Média',
            self::ALTA => 'Alta',
            self::CRITICA => 'Crítica',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::BAIXA => 'success',
            self::MEDIA => 'info',
            self::ALTA => 'warning',
            self::CRITICA => 'danger',
        };
    }

    public function classesBadge(): string
    {
        return match ($this) {
            self::BAIXA => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            self::MEDIA => 'bg-sky-100 text-sky-800 ring-sky-200',
            self::ALTA => 'bg-amber-100 text-amber-800 ring-amber-200',
            self::CRITICA => 'bg-red-100 text-red-800 ring-red-200',
        };
    }

    public static function normalizar(string|self $valor): self
    {
        return $valor instanceof self ? $valor : self::from($valor);
    }

    /**
     * @return array<string, string>
     */
    public static function opcoes(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $complexidade) => [$complexidade->value => $complexidade->rotulo()])
            ->all();
    }
}
