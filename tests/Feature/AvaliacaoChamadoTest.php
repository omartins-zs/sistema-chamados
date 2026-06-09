<?php

namespace Tests\Feature;

use App\Enums\StatusChamadoEnum;
use App\Exceptions\AvaliacaoChamadoException;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Services\AvaliacaoChamadoService;
use App\Services\ChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\DadosFinalizacaoChamado;
use Tests\TestCase;

class AvaliacaoChamadoTest extends TestCase
{
    use DadosFinalizacaoChamado;
    use RefreshDatabase;

    private AvaliacaoChamadoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->service = app(AvaliacaoChamadoService::class);
    }

    public function test_solicitante_consegue_avaliar_chamado_finalizado(): void
    {
        Queue::fake();

        $chamado = $this->criarChamadoFinalizado();

        $avaliacao = $this->service->criar($chamado, $chamado->token_avaliacao, [
            'nota_satisfacao' => 5,
            'nota_tempo_resolucao' => 4,
            'comentario' => 'Excelente atendimento.',
        ]);

        $this->assertSame(5, $avaliacao->nota_satisfacao);
        $this->assertSame(4, $avaliacao->nota_tempo_resolucao);
    }

    public function test_nao_permite_avaliar_chamado_nao_finalizado(): void
    {
        $chamado = Chamado::factory()->create([
            'status' => StatusChamadoEnum::EM_ANDAMENTO,
            'token_avaliacao' => 'token-valido',
        ]);

        $this->expectException(AvaliacaoChamadoException::class);

        $this->service->validarChamadoParaAvaliacao($chamado);
    }

    public function test_nao_permite_avaliar_duas_vezes(): void
    {
        Queue::fake();

        $chamado = $this->criarChamadoFinalizado();

        AvaliacaoChamado::factory()->create(['chamado_id' => $chamado->id]);

        $this->expectException(AvaliacaoChamadoException::class);

        $this->service->validarChamadoParaAvaliacao($chamado->refresh());
    }

    public function test_nao_permite_token_invalido(): void
    {
        $chamado = Chamado::factory()->finalizado()->create();

        $this->expectException(AvaliacaoChamadoException::class);

        $this->service->validarToken($chamado, 'token-invalido');
    }

    public function test_avaliacao_via_http(): void
    {
        Queue::fake();

        $chamado = $this->criarChamadoFinalizado();

        $response = $this->post(route('chamados.avaliar.salvar', [
            'protocolo' => $chamado->protocolo,
            'token' => $chamado->token_avaliacao,
        ]), [
            'nota_satisfacao' => 5,
            'nota_tempo_resolucao' => 5,
            'comentario' => 'Ótimo!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('avaliacoes_chamados', 1);
    }

    public function test_tela_finalizado_exibe_historico_publico(): void
    {
        Queue::fake();

        $chamado = $this->criarChamadoFinalizado();

        $response = $this->get(route('chamados.finalizado', $chamado->protocolo));

        $response->assertOk();
        $response->assertSee($chamado->protocolo);
    }

    private function criarChamadoFinalizado(): Chamado
    {
        $chamado = Chamado::factory()->create();
        $tecnico = $this->tecnicoDoChamado($chamado);

        return app(ChamadoService::class)->finalizar($chamado, $tecnico, $this->dadosFinalizacaoChamado());
    }
}
