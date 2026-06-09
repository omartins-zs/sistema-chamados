<?php

namespace Tests\Unit;

use App\Enums\ComplexidadeChamadoEnum;
use App\Jobs\EnviarEmailChamadoCriadoJob;
use App\Models\Chamado;
use App\Models\Setor;
use App\Services\ChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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

    public function test_criar_chamado_dispara_job_e_carrega_setor(): void
    {
        Queue::fake();

        $setor = Setor::query()->firstOrFail();
        $chamado = $this->service->criar([
            'nome_solicitante' => 'João Souza',
            'email_solicitante' => 'joao@example.com',
            'telefone_solicitante' => '(11) 97777-6666',
            'titulo' => 'Impressora com defeito',
            'descricao' => 'A impressora não liga após queda de energia.',
            'complexidade' => ComplexidadeChamadoEnum::ALTA->value,
            'setor_id' => $setor->id,
        ]);

        $this->assertSame($setor->id, $chamado->setor->id);
        Queue::assertPushed(EnviarEmailChamadoCriadoJob::class);
    }

    public function test_buscar_por_protocolo_carrega_relacionamentos(): void
    {
        $chamado = Chamado::factory()->create();

        $resultado = $this->service->buscarPorProtocolo($chamado->protocolo);

        $this->assertTrue($resultado->relationLoaded('setor'));
        $this->assertTrue($resultado->relationLoaded('historicosPublicos'));
        $this->assertTrue($resultado->relationLoaded('avaliacao'));
    }
}
