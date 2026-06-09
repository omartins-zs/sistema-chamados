<?php

namespace Tests\Unit;

use App\Enums\StatusChamadoEnum;
use App\Exceptions\AvaliacaoChamadoException;
use App\Models\Chamado;
use App\Services\AvaliacaoChamadoService;
use App\Services\ChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AvaliacaoChamadoServiceTest extends TestCase
{
    use RefreshDatabase;

    private AvaliacaoChamadoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->service = app(AvaliacaoChamadoService::class);
    }

    public function test_token_expirado_lanca_excecao(): void
    {
        $chamado = Chamado::factory()->create([
            'status' => StatusChamadoEnum::FINALIZADO,
            'token_avaliacao' => 'token-valido',
            'expira_token_avaliacao_em' => now()->subDay(),
        ]);

        $this->expectException(AvaliacaoChamadoException::class);
        $this->expectExceptionMessage('O link de avaliação expirou.');

        $this->service->validarToken($chamado, 'token-valido');
    }

    public function test_criar_avaliacao_com_dados_validos(): void
    {
        Queue::fake();

        $chamado = app(ChamadoService::class)->finalizar(Chamado::factory()->create());

        $avaliacao = $this->service->criar($chamado, $chamado->token_avaliacao, [
            'nota_satisfacao' => 3,
            'nota_tempo_resolucao' => 4,
        ]);

        $this->assertSame(3, $avaliacao->nota_satisfacao);
        $this->assertSame(4, $avaliacao->nota_tempo_resolucao);
    }

    public function test_nao_permite_segunda_avaliacao(): void
    {
        Queue::fake();

        $chamado = app(ChamadoService::class)->finalizar(Chamado::factory()->create());

        $this->service->criar($chamado, $chamado->token_avaliacao, [
            'nota_satisfacao' => 5,
            'nota_tempo_resolucao' => 5,
        ]);

        $this->expectException(AvaliacaoChamadoException::class);

        $this->service->criar($chamado->refresh(), $chamado->token_avaliacao, [
            'nota_satisfacao' => 1,
            'nota_tempo_resolucao' => 1,
        ]);
    }
}
