<?php

namespace App\Policies;

use App\Models\Chamado;
use App\Models\Usuario;

class ChamadoPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Chamado $chamado): bool
    {
        return $usuario->ehAdministrador() || $usuario->setor_id === $chamado->setor_id;
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->ehAdministrador();
    }

    public function update(Usuario $usuario, Chamado $chamado): bool
    {
        return $usuario->ehAdministrador() || $usuario->setor_id === $chamado->setor_id;
    }

    public function delete(Usuario $usuario, Chamado $chamado): bool
    {
        return $usuario->ehAdministrador();
    }

    public function assumir(Usuario $usuario, Chamado $chamado): bool
    {
        return $usuario->ehAdministrador() || $usuario->setor_id === $chamado->setor_id;
    }

    public function adicionarHistorico(Usuario $usuario, Chamado $chamado): bool
    {
        return $usuario->ehAdministrador() || $usuario->setor_id === $chamado->setor_id;
    }

    public function finalizar(Usuario $usuario, Chamado $chamado): bool
    {
        return $usuario->ehAdministrador() || $usuario->setor_id === $chamado->setor_id;
    }
}
