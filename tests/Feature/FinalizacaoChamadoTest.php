<?php

namespace Tests\Feature;

use App\Enums\StatusChamadoEnum;
use App\Jobs\EnviarEmailChamadoFinalizadoJob;
use App\Models\Chamado;
use App\Models\Setor;
use App\Models\Usuario;
use App\Services\ChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FinalizacaoChamadoTest extends TestCase
{
    use RefreshDatabase;

    private ChamadoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->service = app(ChamadoService::class);
    }

    public function test_tecnico_consegue_finalizar_chamado(): void
    {
        Queue::fake();

        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create([
            'setor_id' => $setor->id,
            'tecnico_responsavel_id' => $tecnico->id,
        ]);

        $resultado = $this->service->finalizar($chamado);

        $this->assertSame(StatusChamadoEnum::FINALIZADO, $resultado->status);
    }

    public function test_finalizado_em_e_preenchido(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create();

        $resultado = $this->service->finalizar($chamado);

        $this->assertNotNull($resultado->finalizado_em);
    }

    public function test_token_de_avaliacao_e_criado(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create();

        $resultado = $this->service->finalizar($chamado);

        $this->assertNotNull($resultado->token_avaliacao);
        $this->assertNotNull($resultado->expira_token_avaliacao_em);
    }

    public function test_email_de_avaliacao_e_disparado(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create();

        $this->service->finalizar($chamado);

        Queue::assertPushed(EnviarEmailChamadoFinalizadoJob::class);
    }

    public function test_status_vira_finalizado(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create(['status' => StatusChamadoEnum::EM_ANDAMENTO]);

        $this->service->finalizar($chamado);

        $this->assertSame(StatusChamadoEnum::FINALIZADO, $chamado->refresh()->status);
    }

    public function test_gerar_protocolo_sequencial(): void
    {
        $protocolo1 = $this->service->gerarProtocolo();
        Chamado::factory()->create(['protocolo' => $protocolo1]);
        $protocolo2 = $this->service->gerarProtocolo();

        $numero1 = (int) substr($protocolo1, -6);
        $numero2 = (int) substr($protocolo2, -6);

        $this->assertSame($numero1 + 1, $numero2);
    }
}
