<?php

namespace Tests\Unit;

use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Setor;
use App\Models\Usuario;
use App\Services\NotificacaoChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class NotificacaoChamadoServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificacaoChamadoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->service = app(NotificacaoChamadoService::class);
    }

    public function test_notificar_chamado_criado(): void
    {
        $chamado = Chamado::factory()->create();

        $this->service->notificarChamadoCriado($chamado);

        $this->assertTrue(true);
    }

    public function test_notificar_chamado_assumido(): void
    {
        $chamado = Chamado::factory()->create();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $chamado->setor_id]);

        $this->service->notificarChamadoAssumido($chamado, $tecnico);

        $this->assertTrue(true);
    }

    public function test_notificar_historico_adicionado(): void
    {
        $historico = HistoricoChamado::factory()->create();

        $this->service->notificarHistoricoAdicionado($historico);

        $this->assertTrue(true);
    }

    public function test_notificar_status_alterado(): void
    {
        $chamado = Chamado::factory()->create();

        $this->service->notificarStatusAlterado($chamado, $chamado->status);

        $this->assertTrue(true);
    }

    public function test_notificar_chamado_finalizado(): void
    {
        $chamado = Chamado::factory()->finalizado()->create();

        $this->service->notificarChamadoFinalizado($chamado);

        $this->assertTrue(true);
    }

    public function test_notificar_email_avaliacao_enviado(): void
    {
        $chamado = Chamado::factory()->finalizado()->create();

        $this->service->notificarEmailAvaliacaoEnviado($chamado);

        $this->assertTrue(true);
    }

    public function test_notificar_tecnico_criado(): void
    {
        $usuario = Usuario::factory()->tecnico()->create();

        $this->service->notificarTecnicoCriado($usuario);

        $this->assertTrue(true);
    }

    public function test_notificar_setor_criado(): void
    {
        $setor = Setor::query()->firstOrFail();

        $this->service->notificarSetorCriado($setor);

        $this->assertTrue(true);
    }

    public function test_notificar_avaliacao_registrada(): void
    {
        $avaliacao = AvaliacaoChamado::factory()->create();

        $this->service->notificarAvaliacaoRegistrada($avaliacao);

        $this->assertTrue(true);
    }

    public function test_enviar_notificacao_tipo_danger(): void
    {
        $metodo = new ReflectionMethod($this->service, 'enviarNotificacao');
        $metodo->setAccessible(true);
        $metodo->invoke($this->service, 'Alerta', 'Mensagem de perigo', 'danger');

        $this->assertTrue(true);
    }
}
