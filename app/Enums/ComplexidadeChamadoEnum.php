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
            self::BAIXA => 'bg-emerald-100 text-emerald-900 ring-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-200 dark:ring-emerald-700',
            self::MEDIA => 'bg-sky-100 text-sky-900 ring-sky-300 dark:bg-sky-950/60 dark:text-sky-200 dark:ring-sky-700',
            self::ALTA => 'bg-amber-100 text-amber-900 ring-amber-300 dark:bg-amber-950/60 dark:text-amber-200 dark:ring-amber-700',
            self::CRITICA => 'bg-red-100 text-red-900 ring-red-300 dark:bg-red-950/60 dark:text-red-200 dark:ring-red-700',
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
