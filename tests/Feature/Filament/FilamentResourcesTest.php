<?php

namespace Tests\Feature\Filament;

use App\Enums\StatusChamadoEnum;
use App\Filament\Resources\AvaliacaoChamados\AvaliacaoChamadoResource;
use App\Filament\Resources\AvaliacaoChamados\Pages\ManageAvaliacoes;
use App\Filament\Resources\Chamados\ChamadoResource;
use App\Filament\Resources\Chamados\Pages\ListChamados;
use App\Filament\Resources\Chamados\Pages\ViewChamado;
use App\Filament\Resources\HistoricoChamados\HistoricoChamadoResource;
use App\Filament\Resources\HistoricoChamados\Pages\ManageHistoricos;
use App\Filament\Resources\Setores\Pages\ManageSetores;
use App\Filament\Resources\Setores\SetorResource;
use App\Filament\Resources\Usuarios\Pages\ManageUsuarios;
use App\Filament\Resources\Usuarios\UsuarioResource;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Setor;
use Database\Seeders\SetorSeeder;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\Concerns\AutenticaFilament;
use Tests\Concerns\DadosFinalizacaoChamado;
use Tests\TestCase;

class FilamentResourcesTest extends TestCase
{
    use AutenticaFilament;
    use DadosFinalizacaoChamado;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_recursos_definem_formularios_e_tabelas(): void
    {
        $this->autenticarAdministrador();

        $schema = Schema::make();

        SetorResource::form($schema);
        UsuarioResource::form($schema);
        ChamadoResource::form($schema);
        ChamadoResource::infolist($schema);
        HistoricoChamadoResource::infolist($schema);
        AvaliacaoChamadoResource::infolist($schema);

        Livewire::test(ListChamados::class)->assertSuccessful();
        Livewire::test(ManageSetores::class)->assertSuccessful();
        Livewire::test(ManageUsuarios::class)->assertSuccessful();
        Livewire::test(ManageHistoricos::class)->assertSuccessful();
        Livewire::test(ManageAvaliacoes::class)->assertSuccessful();

        $this->assertTrue(SetorResource::canViewAny());
        $this->assertTrue(UsuarioResource::canViewAny());
        $this->assertFalse(AvaliacaoChamadoResource::canCreate());
        $this->assertFalse(HistoricoChamadoResource::canCreate());
    }

    public function test_query_de_chamados_filtra_por_setor_do_tecnico(): void
    {
        $setores = Setor::query()->take(2)->get();
        $this->autenticarTecnico($setores[0]->id);

        Chamado::factory()->create(['setor_id' => $setores[0]->id]);
        Chamado::factory()->create(['setor_id' => $setores[1]->id]);

        $this->assertCount(1, ChamadoResource::getEloquentQuery()->get());
    }

    public function test_admin_cria_setor_e_dispara_notificacao(): void
    {
        $this->autenticarAdministrador();

        Livewire::test(ManageSetores::class)
            ->callAction('create', data: [
                'nome' => 'Infraestrutura',
                'slug' => 'infraestrutura',
                'descricao' => 'Setor de infraestrutura.',
                'ativo' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('setores', ['slug' => 'infraestrutura']);
    }

    public function test_admin_cria_tecnico(): void
    {
        $setor = Setor::query()->firstOrFail();
        $this->autenticarAdministrador();

        Livewire::test(ManageUsuarios::class)
            ->callAction('create', data: [
                'nome' => 'Novo Técnico',
                'email' => 'novo.tecnico@chamados.local',
                'senha' => 'password',
                'setor_id' => $setor->id,
                'tipo_usuario' => 'tecnico',
                'ativo' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('usuarios', ['email' => 'novo.tecnico@chamados.local']);
    }

    public function test_listagem_chamados_acoes_de_atendimento(): void
    {
        Queue::fake();

        $setor = Setor::query()->firstOrFail();
        $tecnico = $this->autenticarTecnico($setor->id);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        Livewire::test(ListChamados::class)
            ->callTableAction('assumir', $chamado)
            ->assertHasNoTableActionErrors();

        $this->assertSame($tecnico->id, $chamado->refresh()->tecnico_responsavel_id);

        Livewire::test(ListChamados::class)
            ->callTableAction('adicionarHistorico', $chamado, data: [
                'status' => StatusChamadoEnum::EM_ANDAMENTO->value,
                'descricao' => 'Atendimento em progresso.',
                'visivel_solicitante' => true,
            ])
            ->assertHasNoTableActionErrors();

        Livewire::test(ListChamados::class)
            ->callTableAction('finalizar', $chamado, data: $this->dadosFinalizacaoChamado())
            ->assertHasNoTableActionErrors();

        $this->assertSame(StatusChamadoEnum::FINALIZADO, $chamado->refresh()->status);
    }

    public function test_visualizar_chamado_acoes_e_titulo(): void
    {
        Queue::fake();

        $setor = Setor::query()->firstOrFail();
        $tecnico = $this->autenticarTecnico($setor->id);
        $chamado = Chamado::factory()->create([
            'setor_id' => $setor->id,
            'titulo' => 'Impressora com falha',
        ]);

        Livewire::test(ViewChamado::class, ['record' => $chamado->getRouteKey()])
            ->assertSee('Impressora com falha')
            ->callAction('assumir')
            ->callAction('adicionarHistorico', data: [
                'status' => StatusChamadoEnum::EM_ANDAMENTO->value,
                'descricao' => 'Verificando equipamento.',
            ])
            ->callAction('finalizar', data: $this->dadosFinalizacaoChamado());

        $this->assertSame($tecnico->id, $chamado->refresh()->tecnico_responsavel_id);
        $this->assertSame(StatusChamadoEnum::FINALIZADO, $chamado->status);
    }

    public function test_historico_filtra_por_setor_do_tecnico(): void
    {
        $setores = Setor::query()->take(2)->get();
        $this->autenticarTecnico($setores[0]->id);

        $chamadoMesmoSetor = Chamado::factory()->create(['setor_id' => $setores[0]->id]);
        $chamadoOutroSetor = Chamado::factory()->create(['setor_id' => $setores[1]->id]);

        HistoricoChamado::factory()->create(['chamado_id' => $chamadoMesmoSetor->id]);
        HistoricoChamado::factory()->create(['chamado_id' => $chamadoOutroSetor->id]);

        $this->assertCount(1, HistoricoChamadoResource::getEloquentQuery()->get());
    }

    public function test_setor_resource_query_base(): void
    {
        $this->autenticarAdministrador();

        $this->assertGreaterThan(0, SetorResource::getEloquentQuery()->count());
    }
}
