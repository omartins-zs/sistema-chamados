<?php

namespace App\Enums;

enum StatusChamadoEnum: string
{
    case EM_ABERTO = 'em_aberto';
    case ACESSADO = 'acessado';
    case EM_ANDAMENTO = 'em_andamento';
    case AGUARDANDO_CLIENTE = 'aguardando_cliente';
    case AGUARDANDO_TERCEIROS = 'aguardando_terceiros';
    case PAUSADO = 'pausado';
    case BLOQUEADO = 'bloqueado';
    case CONCLUIDO = 'concluido';
    case FINALIZADO = 'finalizado';
    case CANCELADO = 'cancelado';

    public function rotulo(): string
    {
        return match ($this) {
            self::EM_ABERTO => 'Em Aberto',
            self::ACESSADO => 'Acessado',
            self::EM_ANDAMENTO => 'Em Andamento',
            self::AGUARDANDO_CLIENTE => 'Aguardando Cliente',
            self::AGUARDANDO_TERCEIROS => 'Aguardando Terceiros',
            self::PAUSADO => 'Pausado',
            self::BLOQUEADO => 'Bloqueado',
            self::CONCLUIDO => 'Concluído',
            self::FINALIZADO => 'Finalizado',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::EM_ABERTO => 'gray',
            self::ACESSADO => 'info',
            self::EM_ANDAMENTO => 'warning',
            self::AGUARDANDO_CLIENTE => 'primary',
            self::AGUARDANDO_TERCEIROS => 'purple',
            self::PAUSADO => 'orange',
            self::BLOQUEADO => 'danger',
            self::CONCLUIDO => 'success',
            self::FINALIZADO => 'success',
            self::CANCELADO => 'danger',
        };
    }

    public function classesBadge(): string
    {
        return match ($this) {
            self::EM_ABERTO => 'bg-gray-100 text-gray-800 ring-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600',
            self::ACESSADO => 'bg-sky-100 text-sky-900 ring-sky-300 dark:bg-sky-950/60 dark:text-sky-200 dark:ring-sky-700',
            self::EM_ANDAMENTO => 'bg-amber-100 text-amber-900 ring-amber-300 dark:bg-amber-950/60 dark:text-amber-200 dark:ring-amber-700',
            self::AGUARDANDO_CLIENTE => 'bg-blue-100 text-blue-900 ring-blue-300 dark:bg-blue-950/60 dark:text-blue-200 dark:ring-blue-700',
            self::AGUARDANDO_TERCEIROS => 'bg-violet-100 text-violet-900 ring-violet-300 dark:bg-violet-950/60 dark:text-violet-200 dark:ring-violet-700',
            self::PAUSADO => 'bg-orange-100 text-orange-900 ring-orange-300 dark:bg-orange-950/60 dark:text-orange-200 dark:ring-orange-700',
            self::BLOQUEADO => 'bg-red-100 text-red-900 ring-red-300 dark:bg-red-950/60 dark:text-red-200 dark:ring-red-700',
            self::CONCLUIDO => 'bg-emerald-100 text-emerald-900 ring-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-200 dark:ring-emerald-700',
            self::FINALIZADO => 'bg-green-100 text-green-900 ring-green-300 dark:bg-green-950/60 dark:text-green-200 dark:ring-green-700',
            self::CANCELADO => 'bg-red-100 text-red-900 ring-red-300 dark:bg-red-950/60 dark:text-red-200 dark:ring-red-700',
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
            ->mapWithKeys(fn (self $status) => [$status->value => $status->rotulo()])
            ->all();
    }
}
