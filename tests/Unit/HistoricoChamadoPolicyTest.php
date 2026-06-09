<?php

namespace Tests\Unit;

use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Setor;
use App\Models\Usuario;
use App\Policies\HistoricoChamadoPolicy;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricoChamadoPolicyTest extends TestCase
{
    use RefreshDatabase;

    private HistoricoChamadoPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->policy = new HistoricoChamadoPolicy;
    }

    public function test_view_any_sempre_permitido(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();

        $this->assertTrue($this->policy->viewAny($tecnico));
    }

    public function test_admin_visualiza_qualquer_historico(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $historico = HistoricoChamado::factory()->create();

        $this->assertTrue($this->policy->view($admin, $historico));
    }

    public function test_tecnico_visualiza_historico_do_mesmo_setor(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);
        $historico = HistoricoChamado::factory()->create(['chamado_id' => $chamado->id]);

        $this->assertTrue($this->policy->view($tecnico, $historico));
    }

    public function test_tecnico_nao_visualiza_historico_de_outro_setor(): void
    {
        $setores = Setor::query()->take(2)->get();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setores[0]->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setores[1]->id]);
        $historico = HistoricoChamado::factory()->create(['chamado_id' => $chamado->id]);

        $this->assertFalse($this->policy->view($tecnico, $historico));
    }
}
