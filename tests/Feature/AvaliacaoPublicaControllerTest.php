<?php

namespace Tests\Feature;

use App\Enums\StatusChamadoEnum;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Services\ChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\DadosFinalizacaoChamado;
use Tests\TestCase;

class AvaliacaoPublicaControllerTest extends TestCase
{
    use DadosFinalizacaoChamado;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_exibir_formulario_com_token_valido(): void
    {
        Queue::fake();

        $chamado = $this->criarChamadoFinalizado();

        $response = $this->get(route('chamados.avaliar', [
            'protocolo' => $chamado->protocolo,
            'token' => $chamado->token_avaliacao,
        ]));

        $response->assertOk();
        $response->assertSee($chamado->protocolo);
    }

    public function test_exibir_redireciona_quando_protocolo_inexistente(): void
    {
        $response = $this->get(route('chamados.avaliar', [
            'protocolo' => 'CHM-2099-000999',
            'token' => 'token-invalido',
        ]));

        $response->assertRedirect(route('chamados.criar'));
        $response->assertSessionHas('erro');
    }

    public function test_exibir_redireciona_com_token_invalido(): void
    {
        $chamado = Chamado::factory()->finalizado()->create([
            'token_avaliacao' => 'token-correto',
        ]);

        $response = $this->get(route('chamados.avaliar', [
            'protocolo' => $chamado->protocolo,
            'token' => 'token-invalido',
        ]));

        $response->assertRedirect(route('chamados.criar'));
        $response->assertSessionHas('erro');
    }

    public function test_exibir_redireciona_quando_chamado_nao_finalizado(): void
    {
        $chamado = Chamado::factory()->create([
            'status' => StatusChamadoEnum::EM_ANDAMENTO,
            'token_avaliacao' => 'token-valido',
        ]);

        $response = $this->get(route('chamados.avaliar', [
            'protocolo' => $chamado->protocolo,
            'token' => 'token-valido',
        ]));

        $response->assertRedirect(route('chamados.criar'));
        $response->assertSessionHas('erro');
    }

    public function test_exibir_redireciona_quando_ja_avaliado(): void
    {
        Queue::fake();

        $chamado = $this->criarChamadoFinalizado();
        AvaliacaoChamado::factory()->create(['chamado_id' => $chamado->id]);

        $response = $this->get(route('chamados.avaliar', [
            'protocolo' => $chamado->protocolo,
            'token' => $chamado->token_avaliacao,
        ]));

        $response->assertRedirect(route('chamados.criar'));
        $response->assertSessionHas('erro');
    }

    public function test_salvar_redireciona_quando_protocolo_inexistente(): void
    {
        $response = $this->post(route('chamados.avaliar.salvar', [
            'protocolo' => 'CHM-2099-000999',
            'token' => 'token-invalido',
        ]), [
            'nota_satisfacao' => 5,
            'nota_tempo_resolucao' => 5,
        ]);

        $response->assertRedirect(route('chamados.criar'));
        $response->assertSessionHas('erro');
    }

    public function test_salvar_redireciona_com_token_invalido(): void
    {
        $chamado = Chamado::factory()->finalizado()->create([
            'token_avaliacao' => 'token-correto',
        ]);

        $response = $this->post(route('chamados.avaliar.salvar', [
            'protocolo' => $chamado->protocolo,
            'token' => 'token-invalido',
        ]), [
            'nota_satisfacao' => 5,
            'nota_tempo_resolucao' => 5,
        ]);

        $response->assertRedirect(route('chamados.criar'));
        $response->assertSessionHas('erro');
    }

    private function criarChamadoFinalizado(): Chamado
    {
        $chamado = Chamado::factory()->create();
        $tecnico = $this->tecnicoDoChamado($chamado);

        return app(ChamadoService::class)->finalizar($chamado, $tecnico, $this->dadosFinalizacaoChamado());
    }
}
