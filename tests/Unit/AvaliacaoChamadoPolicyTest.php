<?php

namespace Tests\Unit;

use App\Models\AvaliacaoChamado;
use App\Models\Usuario;
use App\Policies\AvaliacaoChamadoPolicy;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvaliacaoChamadoPolicyTest extends TestCase
{
    use RefreshDatabase;

    private AvaliacaoChamadoPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->policy = new AvaliacaoChamadoPolicy;
    }

    public function test_view_any_sempre_permitido(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();

        $this->assertTrue($this->policy->viewAny($tecnico));
    }

    public function test_view_sempre_permitido(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();
        $avaliacao = AvaliacaoChamado::factory()->create();

        $this->assertTrue($this->policy->view($tecnico, $avaliacao));
    }

    public function test_create_sempre_negado(): void
    {
        $admin = Usuario::factory()->administrador()->create();

        $this->assertFalse($this->policy->create($admin));
    }

    public function test_admin_pode_atualizar_e_excluir(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $avaliacao = AvaliacaoChamado::factory()->create();

        $this->assertTrue($this->policy->update($admin, $avaliacao));
        $this->assertTrue($this->policy->delete($admin, $avaliacao));
    }

    public function test_tecnico_nao_pode_atualizar_nem_excluir(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();
        $avaliacao = AvaliacaoChamado::factory()->create();

        $this->assertFalse($this->policy->update($tecnico, $avaliacao));
        $this->assertFalse($this->policy->delete($tecnico, $avaliacao));
    }
}
