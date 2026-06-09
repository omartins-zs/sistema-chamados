<?php

namespace Tests\Unit;

use App\Enums\StatusChamadoEnum;
use App\Models\Chamado;
use App\Services\ChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChamadoServiceAlterarStatusTest extends TestCase
{
    use RefreshDatabase;

    private ChamadoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->service = app(ChamadoService::class);
    }

    public function test_alterar_status_atualiza_chamado(): void
    {
        $chamado = Chamado::factory()->create([
            'status' => StatusChamadoEnum::EM_ABERTO,
        ]);

        $resultado = $this->service->alterarStatus($chamado, StatusChamadoEnum::PAUSADO);

        $this->assertSame(StatusChamadoEnum::PAUSADO, $resultado->status);
    }
}
