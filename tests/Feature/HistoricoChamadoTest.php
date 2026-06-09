<?php

namespace Tests\Feature;

use App\Enums\StatusChamadoEnum;
use App\Exceptions\ChamadoNaoPertenceAoSetorException;
use App\Models\Chamado;
use App\Models\Setor;
use App\Models\Usuario;
use App\Services\HistoricoChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricoChamadoTest extends TestCase
{
    use RefreshDatabase;

    private HistoricoChamadoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->service = app(HistoricoChamadoService::class);
    }

    public function test_tecnico_consegue_adicionar_historico(): void
    {
        $setor = Setor::query()->where('nome', 'Desenvolvimento')->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $historico = $this->service->adicionar($chamado, $tecnico, [
            'status' => StatusChamadoEnum::EM_ANDAMENTO->value,
            'descricao' => 'Iniciando estrutura do site.',
            'visivel_solicitante' => true,
        ]);

        $this->assertDatabaseHas('historicos_chamados', [
            'id' => $historico->id,
            'tecnico_id' => $tecnico->id,
            'descricao' => 'Iniciando estrutura do site.',
        ]);
    }

    public function test_primeiro_tecnico_que_adiciona_historico_assume_chamado(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $this->service->adicionar($chamado, $tecnico, [
            'status' => StatusChamadoEnum::ACESSADO->value,
            'descricao' => 'Disponibilizando ambiente.',
        ]);

        $chamado->refresh();

        $this->assertSame($tecnico->id, $chamado->tecnico_responsavel_id);
    }

    public function test_status_muda_ao_adicionar_historico(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $this->service->adicionar($chamado, $tecnico, [
            'status' => StatusChamadoEnum::EM_ANDAMENTO->value,
            'descricao' => 'Atendimento iniciado.',
        ]);

        $this->assertSame(StatusChamadoEnum::EM_ANDAMENTO, $chamado->refresh()->status);
    }

    public function test_historico_salva_data_tecnico_descricao_e_status(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $historico = $this->service->adicionar($chamado, $tecnico, [
            'status' => StatusChamadoEnum::AGUARDANDO_CLIENTE->value,
            'descricao' => 'Aguardando retorno do cliente.',
        ]);

        $this->assertNotNull($historico->created_at);
        $this->assertSame($tecnico->id, $historico->tecnico_id);
        $this->assertSame(StatusChamadoEnum::AGUARDANDO_CLIENTE, $historico->status);
    }

    public function test_tecnico_nao_deve_atuar_em_chamado_de_outro_setor(): void
    {
        $setores = Setor::query()->take(2)->get();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setores[0]->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setores[1]->id]);

        $this->expectException(ChamadoNaoPertenceAoSetorException::class);

        $this->service->adicionar($chamado, $tecnico, [
            'status' => StatusChamadoEnum::ACESSADO->value,
            'descricao' => 'Tentativa inválida.',
        ]);
    }

    public function test_admin_pode_adicionar_historico_em_qualquer_setor(): void
    {
        $setor = Setor::query()->firstOrFail();
        $admin = Usuario::factory()->administrador()->create();
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $historico = $this->service->adicionar($chamado, $admin, [
            'status' => StatusChamadoEnum::ACESSADO->value,
            'descricao' => 'Admin assumindo acompanhamento.',
        ]);

        $this->assertNotNull($historico->id);
    }

    public function test_assumir_chamado_define_tecnico_responsavel(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $this->service->assumirChamado($chamado, $tecnico);

        $this->assertSame($tecnico->id, $chamado->refresh()->tecnico_responsavel_id);
        $this->assertSame(StatusChamadoEnum::ACESSADO, $chamado->status);
    }

    public function test_assumir_chamado_ja_assumido_nao_altera_responsavel(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico1 = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $tecnico2 = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create([
            'setor_id' => $setor->id,
            'tecnico_responsavel_id' => $tecnico1->id,
        ]);

        $this->service->assumirChamado($chamado, $tecnico2);

        $this->assertSame($tecnico1->id, $chamado->refresh()->tecnico_responsavel_id);
    }

    public function test_registrar_finalizacao_valida_setor_do_tecnico(): void
    {
        $setores = Setor::query()->take(2)->get();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setores[0]->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setores[1]->id]);

        $this->expectException(ChamadoNaoPertenceAoSetorException::class);

        $this->service->registrarFinalizacao($chamado, $tecnico, 'Motivo', 'Descrição detalhada.');
    }

    public function test_adicionar_historico_sem_assumir_quando_ja_tem_responsavel(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico1 = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $tecnico2 = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create([
            'setor_id' => $setor->id,
            'tecnico_responsavel_id' => $tecnico1->id,
        ]);

        $this->service->adicionar($chamado, $tecnico2, [
            'status' => StatusChamadoEnum::EM_ANDAMENTO->value,
            'descricao' => 'Continuidade do atendimento.',
        ]);

        $this->assertSame($tecnico1->id, $chamado->refresh()->tecnico_responsavel_id);
    }
}
