<?php

namespace Tests\Unit;

use App\Enums\StatusChamadoEnum;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChamadoModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_esta_finalizado_retorna_verdadeiro(): void
    {
        $chamado = Chamado::factory()->create([
            'status' => StatusChamadoEnum::FINALIZADO,
        ]);

        $this->assertTrue($chamado->estaFinalizado());
    }

    public function test_pode_ser_avaliado_quando_finalizado_sem_avaliacao(): void
    {
        $chamado = Chamado::factory()->finalizado()->create();

        $this->assertTrue($chamado->podeSerAvaliado());
    }

    public function test_nao_pode_ser_avaliado_quando_ja_avaliado(): void
    {
        $chamado = Chamado::factory()->finalizado()->create();

        AvaliacaoChamado::factory()->create(['chamado_id' => $chamado->id]);

        $this->assertFalse($chamado->refresh()->podeSerAvaliado());
    }
}
