<?php

namespace Tests\Unit;

use App\Enums\StatusChamadoEnum;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Usuario;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricoChamadoModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_relacionamentos_chamado_e_tecnico(): void
    {
        $historico = HistoricoChamado::factory()->create();

        $this->assertInstanceOf(Chamado::class, $historico->chamado);
        $this->assertInstanceOf(Usuario::class, $historico->tecnico);
    }

    public function test_casts_status_e_visibilidade(): void
    {
        $historico = HistoricoChamado::factory()->create([
            'status' => StatusChamadoEnum::EM_ANDAMENTO,
            'visivel_solicitante' => true,
        ]);

        $this->assertSame(StatusChamadoEnum::EM_ANDAMENTO, $historico->status);
        $this->assertTrue($historico->visivel_solicitante);
    }
}
