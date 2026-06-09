<?php

namespace Tests\Unit;

use App\Enums\TipoUsuarioEnum;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Setor;
use App\Models\Usuario;
use Database\Seeders\SetorSeeder;
use Filament\Facades\Filament;
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

    public function test_can_access_panel_quando_ativo(): void
    {
        $usuario = Usuario::factory()->create(['ativo' => true]);

        $this->assertTrue($usuario->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_can_access_panel_quando_inativo(): void
    {
        $usuario = Usuario::factory()->create(['ativo' => false]);

        $this->assertFalse($usuario->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_relacionamentos_setor_chamados_e_historicos(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create([
            'setor_id' => $setor->id,
            'tecnico_responsavel_id' => $tecnico->id,
        ]);
        HistoricoChamado::factory()->create([
            'chamado_id' => $chamado->id,
            'tecnico_id' => $tecnico->id,
        ]);

        $this->assertSame($setor->id, $tecnico->setor->id);
        $this->assertCount(1, $tecnico->chamadosResponsaveis);
        $this->assertCount(1, $tecnico->historicos);
    }
}
