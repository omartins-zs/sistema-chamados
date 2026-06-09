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
            self::CONCLUIDO => 'success',
            self::FINALIZADO => 'success',
            self::CANCELADO => 'danger',
        };
    }

    public function classesBadge(): string
    {
        return match ($this) {
            self::EM_ABERTO => 'bg-slate-100 text-slate-700 ring-slate-200',
            self::ACESSADO => 'bg-sky-100 text-sky-800 ring-sky-200',
            self::EM_ANDAMENTO => 'bg-amber-100 text-amber-800 ring-amber-200',
            self::AGUARDANDO_CLIENTE => 'bg-blue-100 text-blue-800 ring-blue-200',
            self::AGUARDANDO_TERCEIROS => 'bg-violet-100 text-violet-800 ring-violet-200',
            self::PAUSADO => 'bg-orange-100 text-orange-800 ring-orange-200',
            self::CONCLUIDO => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            self::FINALIZADO => 'bg-green-100 text-green-800 ring-green-200',
            self::CANCELADO => 'bg-red-100 text-red-800 ring-red-200',
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
