<?php

namespace Tests\Unit;

use App\Enums\TipoUsuarioEnum;
use App\Models\Usuario;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_administrador_identificado_corretamente(): void
    {
        $admin = Usuario::factory()->administrador()->create();

        $this->assertTrue($admin->ehAdministrador());
        $this->assertFalse($admin->ehTecnico());
    }

    public function test_tecnico_identificado_corretamente(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();

        $this->assertTrue($tecnico->ehTecnico());
        $this->assertFalse($tecnico->ehAdministrador());
    }

    public function test_senha_usa_campo_customizado(): void
    {
        $usuario = Usuario::factory()->create([
            'senha' => 'password',
            'tipo_usuario' => TipoUsuarioEnum::TECNICO,
        ]);

        $this->assertSame('senha', $usuario->getAuthPasswordName());
    }

    public function test_nome_exposto_ao_filament(): void
    {
        $usuario = Usuario::factory()->create(['nome' => 'Administrador']);

        $this->assertSame('Administrador', $usuario->getFilamentName());
    }
}
