<?php

namespace Tests\Feature;

use App\Enums\StatusChamadoEnum;
use App\Models\Chamado;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultarChamadoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_exibe_formulario_de_consulta(): void
    {
        $response = $this->get(route('chamados.consultar'));

        $response->assertOk();
        $response->assertSee('Consultar Chamado');
    }

    public function test_consulta_chamado_em_andamento(): void
    {
        $chamado = Chamado::factory()->create([
            'status' => StatusChamadoEnum::EM_ANDAMENTO,
        ]);

        $response = $this->post(route('chamados.consultar.buscar'), [
            'protocolo' => $chamado->protocolo,
        ]);

        $response->assertOk();
        $response->assertSee($chamado->protocolo);
        $response->assertSee('Situação Atual');
    }

    public function test_consulta_chamado_finalizado_redireciona(): void
    {
        $chamado = Chamado::factory()->finalizado()->create();

        $response = $this->post(route('chamados.consultar.buscar'), [
            'protocolo' => $chamado->protocolo,
        ]);

        $response->assertRedirect(route('chamados.finalizado', $chamado->protocolo));
    }

    public function test_consulta_protocolo_inexistente_retorna_erro(): void
    {
        $response = $this->from(route('chamados.consultar'))
            ->post(route('chamados.consultar.buscar'), [
                'protocolo' => 'CHM-2026-999999',
            ]);

        $response->assertRedirect(route('chamados.consultar'));
        $response->assertSessionHas('erro');
    }

    public function test_consulta_protocolo_invalido_retorna_erro_validacao(): void
    {
        $response = $this->from(route('chamados.consultar'))
            ->post(route('chamados.consultar.buscar'), [
                'protocolo' => 'INVALIDO',
            ]);

        $response->assertRedirect(route('chamados.consultar'));
        $response->assertSessionHasErrors('protocolo');
    }
}
