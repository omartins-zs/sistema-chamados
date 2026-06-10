<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\Configuracoes;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\AvaliacaoChamados\Pages\ManageAvaliacoes;
use App\Filament\Resources\Chamados\Pages\ListChamados;
use App\Filament\Resources\Chamados\Pages\ViewChamado;
use App\Filament\Resources\HistoricoChamados\Pages\ManageHistoricos;
use App\Filament\Resources\Setores\Pages\ManageSetores;
use App\Filament\Resources\Usuarios\Pages\ManageUsuarios;
use App\Filament\Widgets\ChamadosEmAtendimentoWidget;
use App\Filament\Widgets\ChamadosEncerradosWidget;
use App\Filament\Widgets\ResumoGeralChamadosWidget;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Models\Setor;
use App\Models\Usuario;
use Database\Seeders\SetorSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Tests\Concerns\AutenticaFilament;
use Tests\TestCase;

class PainelFilamentTest extends TestCase
{
    use AutenticaFilament;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_dashboard_carrega_widgets(): void
    {
        $this->autenticarAdministrador();

        Livewire::test(Dashboard::class)
            ->assertSuccessful();
    }

    public function test_widgets_exibem_estatisticas_para_admin(): void
    {
        $this->autenticarAdministrador();
        Chamado::factory()->count(2)->create();

        Livewire::test(ResumoGeralChamadosWidget::class)->assertSuccessful();
        Livewire::test(ChamadosEmAtendimentoWidget::class)->assertSuccessful();
        Livewire::test(ChamadosEncerradosWidget::class)->assertSuccessful();
    }

    public function test_widget_filtra_chamados_por_setor_do_tecnico(): void
    {
        $setores = Setor::query()->take(2)->get();
        $this->autenticarTecnico($setores[0]->id);

        Chamado::factory()->create(['setor_id' => $setores[0]->id]);
        Chamado::factory()->create(['setor_id' => $setores[1]->id]);

        Livewire::test(ResumoGeralChamadosWidget::class)
            ->assertSuccessful();
    }

    public function test_listagem_chamados_acessivel_por_admin(): void
    {
        $this->autenticarAdministrador();

        Livewire::test(ListChamados::class)
            ->assertSuccessful();
    }

    public function test_visualizar_chamado_carrega_pagina(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = $this->autenticarTecnico($setor->id);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        Livewire::test(ViewChamado::class, ['record' => $chamado->getRouteKey()])
            ->assertSuccessful();
    }

    public function test_paginas_de_gestao_acessiveis_por_admin(): void
    {
        $this->autenticarAdministrador();

        Livewire::test(ManageSetores::class)->assertSuccessful();
        Livewire::test(ManageUsuarios::class)->assertSuccessful();
        Livewire::test(ManageHistoricos::class)->assertSuccessful();
        Livewire::test(ManageAvaliacoes::class)->assertSuccessful();
    }

    public function test_configuracoes_apenas_para_admin(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();
        $this->actingAs($tecnico);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->assertFalse(Configuracoes::canAccess());
    }

    public function test_configuracoes_metodos_de_resumo(): void
    {
        $this->autenticarAdministrador();
        Chamado::factory()->count(2)->create();
        AvaliacaoChamado::factory()->create();

        $component = Livewire::test(Configuracoes::class);
        $page = $component->instance();

        $resumo = $page->getResumo();
        $this->assertGreaterThanOrEqual(2, $resumo['chamados']);
        $this->assertGreaterThanOrEqual(1, $resumo['avaliacoes']);
        $this->assertArrayHasKey('setores', $resumo);

        $this->assertNotEmpty($page->getSetoresListagem());
        $this->assertArrayHasKey('rodando', $page->getStatusFila());
    }

    public function test_configuracoes_rotulos_de_ambiente_e_integracao(): void
    {
        $this->autenticarAdministrador();

        Config::set('app.env', 'production');
        Config::set('queue.default', 'redis');
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.empresa.com.br');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.scheme', 'tls');
        Config::set('mail.from.address', 'noreply@empresa.com.br');

        $page = Livewire::test(Configuracoes::class)->instance();
        $info = $page->getInformacoesSistema();

        $this->assertSame('Produção', $info['ambiente_rotulo']);
        $this->assertSame('Redis', $info['fila_rotulo']);
        $this->assertSame('SMTP', $info['mailer_rotulo']);
        $this->assertFalse($info['mailer_alerta']);
        $this->assertSame('smtp.empresa.com.br', $info['mail_host']);
        $this->assertSame(587, $info['mail_port']);
        $this->assertSame('tls', $info['mail_scheme']);
        $this->assertSame('noreply@empresa.com.br', $info['mail_from']);
    }

    public function test_configuracoes_rotulos_de_homologacao_e_log(): void
    {
        $this->autenticarAdministrador();

        Config::set('app.env', 'staging');
        Config::set('queue.default', 'beanstalkd');
        Config::set('mail.default', 'log');

        $page = Livewire::test(Configuracoes::class)->instance();
        $info = $page->getInformacoesSistema();

        $this->assertSame('Homologação', $info['ambiente_rotulo']);
        $this->assertSame('Beanstalkd', $info['fila_rotulo']);
        $this->assertSame('Log (modo teste)', $info['mailer_rotulo']);
        $this->assertTrue($info['mailer_alerta']);
    }

    public function test_configuracoes_rotulos_padrao_e_servicos_externos(): void
    {
        $this->autenticarAdministrador();

        Config::set('app.env', 'custom_env');
        Config::set('queue.default', 'sqs');
        Config::set('mail.default', 'ses');

        $page = Livewire::test(Configuracoes::class)->instance();
        $info = $page->getInformacoesSistema();

        $this->assertSame('Custom_env', $info['ambiente_rotulo']);
        $this->assertSame('Amazon SQS', $info['fila_rotulo']);
        $this->assertSame('Amazon SES', $info['mailer_rotulo']);
    }

    public function test_configuracoes_ambiente_local_e_postmark(): void
    {
        $this->autenticarAdministrador();

        Config::set('app.env', 'local');
        Config::set('mail.default', 'postmark');

        $page = Livewire::test(Configuracoes::class)->instance();
        $info = $page->getInformacoesSistema();

        $this->assertSame('Desenvolvimento Local', $info['ambiente_rotulo']);
        $this->assertSame('Postmark', $info['mailer_rotulo']);
    }
}
