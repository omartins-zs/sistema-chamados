<?php

namespace App\Policies;

use App\Models\Usuario;

class UsuarioPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->ehAdministrador();
    }

    public function view(Usuario $usuario, Usuario $model): bool
    {
        return $usuario->ehAdministrador();
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->ehAdministrador();
    }

    public function update(Usuario $usuario, Usuario $model): bool
    {
        return $usuario->ehAdministrador();
    }

    public function delete(Usuario $usuario, Usuario $model): bool
    {
        return $usuario->ehAdministrador() && $usuario->id !== $model->id;
    }
}
