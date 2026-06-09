<?php

namespace App\Enums;

enum TipoUsuarioEnum: string
{
    case ADMINISTRADOR = 'administrador';
    case TECNICO = 'tecnico';

    public function rotulo(): string
    {
        return match ($this) {
            self::ADMINISTRADOR => 'Administrador',
            self::TECNICO => 'Técnico',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function opcoes(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $tipo) => [$tipo->value => $tipo->rotulo()])
            ->all();
    }
}
