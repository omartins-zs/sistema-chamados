<?php

namespace App\Policies;

use App\Models\Setor;
use App\Models\Usuario;

class SetorPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->ehAdministrador();
    }

    public function view(Usuario $usuario, Setor $setor): bool
    {
        return $usuario->ehAdministrador();
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->ehAdministrador();
    }

    public function update(Usuario $usuario, Setor $setor): bool
    {
        return $usuario->ehAdministrador();
    }

    public function delete(Usuario $usuario, Setor $setor): bool
    {
        return $usuario->ehAdministrador();
    }
}
