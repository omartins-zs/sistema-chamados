<?php

namespace Tests\Unit;

use App\Models\Chamado;
use App\Services\ChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChamadoServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChamadoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->service = app(ChamadoService::class);
    }

    public function test_buscar_por_protocolo_retorna_chamado(): void
    {
        $chamado = Chamado::factory()->create();

        $resultado = $this->service->buscarPorProtocolo($chamado->protocolo);

        $this->assertNotNull($resultado);
        $this->assertSame($chamado->id, $resultado->id);
    }

    public function test_buscar_por_protocolo_inexistente_retorna_null(): void
    {
        $this->assertNull($this->service->buscarPorProtocolo('CHM-2099-000999'));
    }

    public function test_gerar_token_avaliacao_unico(): void
    {
        $token1 = $this->service->gerarTokenAvaliacao();
        Chamado::factory()->create(['token_avaliacao' => $token1]);
        $token2 = $this->service->gerarTokenAvaliacao();

        $this->assertNotSame($token1, $token2);
        $this->assertSame(64, strlen($token2));
    }
}
