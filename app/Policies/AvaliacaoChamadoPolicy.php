<?php

namespace App\Policies;

use App\Models\AvaliacaoChamado;
use App\Models\Usuario;

class AvaliacaoChamadoPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, AvaliacaoChamado $avaliacao): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return false;
    }

    public function update(Usuario $usuario, AvaliacaoChamado $avaliacao): bool
    {
        return $usuario->ehAdministrador();
    }

    public function delete(Usuario $usuario, AvaliacaoChamado $avaliacao): bool
    {
        return $usuario->ehAdministrador();
    }
}
