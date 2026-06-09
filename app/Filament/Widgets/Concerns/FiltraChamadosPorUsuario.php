<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\Chamado;
use Illuminate\Database\Eloquent\Builder;

trait FiltraChamadosPorUsuario
{
    protected function getChamadoQuery(): Builder
    {
        $query = Chamado::query();
        $usuario = auth()->user();

        if ($usuario && ! $usuario->ehAdministrador()) {
            $query->where('setor_id', $usuario->setor_id);
        }

        return $query;
    }
}
