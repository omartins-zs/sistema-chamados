<?php

namespace App\Policies;

use App\Models\HistoricoChamado;
use App\Models\Usuario;

class HistoricoChamadoPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, HistoricoChamado $historico): bool
    {
        if ($usuario->ehAdministrador()) {
            return true;
        }

        $chamado = $historico->chamado;

        return $usuario->setor_id === $chamado->setor_id;
    }
}
