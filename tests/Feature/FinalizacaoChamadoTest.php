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
use Illuminate\Validation\ValidationException;
use Tests\Concerns\DadosFinalizacaoChamado;
use Tests\TestCase;

class FinalizacaoChamadoTest extends TestCase
{
    use DadosFinalizacaoChamado;
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

        $resultado = $this->service->finalizar($chamado, $tecnico, $this->dadosFinalizacaoChamado());

        $this->assertSame(StatusChamadoEnum::FINALIZADO, $resultado->status);
    }

    public function test_finalizado_em_e_preenchido(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create();
        $tecnico = $this->tecnicoDoChamado($chamado);

        $resultado = $this->service->finalizar($chamado, $tecnico, $this->dadosFinalizacaoChamado());

        $this->assertNotNull($resultado->finalizado_em);
    }

    public function test_token_de_avaliacao_e_criado(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create();
        $tecnico = $this->tecnicoDoChamado($chamado);

        $resultado = $this->service->finalizar($chamado, $tecnico, $this->dadosFinalizacaoChamado());

        $this->assertNotNull($resultado->token_avaliacao);
        $this->assertNotNull($resultado->expira_token_avaliacao_em);
    }

    public function test_email_de_avaliacao_e_disparado(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create();
        $tecnico = $this->tecnicoDoChamado($chamado);

        $this->service->finalizar($chamado, $tecnico, $this->dadosFinalizacaoChamado());

        Queue::assertPushed(EnviarEmailChamadoFinalizadoJob::class);
    }

    public function test_status_vira_finalizado(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create(['status' => StatusChamadoEnum::EM_ANDAMENTO]);
        $tecnico = $this->tecnicoDoChamado($chamado);

        $this->service->finalizar($chamado, $tecnico, $this->dadosFinalizacaoChamado());

        $this->assertSame(StatusChamadoEnum::FINALIZADO, $chamado->refresh()->status);
    }

    public function test_finalizacao_registra_historico_com_motivo_e_texto(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create();
        $tecnico = $this->tecnicoDoChamado($chamado);
        $dados = $this->dadosFinalizacaoChamado();

        $this->service->finalizar($chamado, $tecnico, $dados);

        $this->assertDatabaseHas('historicos_chamados', [
            'chamado_id' => $chamado->id,
            'tecnico_id' => $tecnico->id,
            'status' => StatusChamadoEnum::FINALIZADO->value,
            'visivel_solicitante' => true,
        ]);

        $historico = $chamado->historicos()->latest('id')->first();
        $this->assertStringContainsString($dados['motivo'], $historico->descricao);
        $this->assertStringContainsString($dados['descricao'], $historico->descricao);
    }

    public function test_finalizacao_exige_motivo_e_descricao(): void
    {
        Queue::fake();

        $chamado = Chamado::factory()->create();
        $tecnico = $this->tecnicoDoChamado($chamado);

        $this->expectException(ValidationException::class);

        $this->service->finalizar($chamado, $tecnico, [
            'motivo' => '',
            'descricao' => '',
        ]);
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
