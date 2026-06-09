<?php

namespace Tests\Concerns;

use App\Models\Usuario;
use Filament\Facades\Filament;

trait AutenticaFilament
{
    protected function autenticarAdministrador(): Usuario
    {
        $admin = Usuario::factory()->administrador()->create();
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return $admin;
    }

    protected function autenticarTecnico(?int $setorId = null): Usuario
    {
        $tecnico = Usuario::factory()->tecnico()->create([
            'setor_id' => $setorId,
        ]);
        $this->actingAs($tecnico);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return $tecnico;
    }
}
