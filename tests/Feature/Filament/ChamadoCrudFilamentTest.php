<?php

namespace Tests\Feature\Filament;

use App\Enums\StatusChamadoEnum;
use App\Filament\Resources\Chamados\Pages\CreateChamado;
use App\Filament\Resources\Chamados\Pages\EditChamado;
use App\Filament\Widgets\ChamadosEvolucaoWidget;
use App\Models\Chamado;
use App\Models\Setor;
use Database\Seeders\SetorSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\AutenticaFilament;
use Tests\TestCase;

class ChamadoCrudFilamentTest extends TestCase
{
    use AutenticaFilament;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_admin_cria_chamado_pela_pagina_de_criacao(): void
    {
        $this->autenticarAdministrador();
        $setor = Setor::query()->first();

        Livewire::test(CreateChamado::class)
            ->fillForm([
                'nome_solicitante' => 'Maria Silva',
                'email_solicitante' => 'maria@exemplo.com',
                'telefone_solicitante' => '11999990000',
                'titulo' => 'Impressora sem toner',
                'descricao' => 'A impressora do setor financeiro parou de imprimir.',
                'complexidade' => 'media',
                'setor_id' => $setor->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('chamados', [
            'nome_solicitante' => 'Maria Silva',
            'titulo' => 'Impressora sem toner',
            'status' => StatusChamadoEnum::EM_ABERTO->value,
        ]);
    }

    public function test_admin_edita_e_exclui_chamado(): void
    {
        $this->autenticarAdministrador();
        $chamado = Chamado::factory()->create();

        Livewire::test(EditChamado::class, ['record' => $chamado->getRouteKey()])
            ->fillForm([
                'status' => StatusChamadoEnum::EM_ANDAMENTO->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(StatusChamadoEnum::EM_ANDAMENTO, $chamado->refresh()->status);

        Livewire::test(EditChamado::class, ['record' => $chamado->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('chamados', ['id' => $chamado->id]);
    }

    public function test_widget_de_evolucao_carrega_para_admin(): void
    {
        $this->autenticarAdministrador();
        Chamado::factory()->count(2)->create();

        Livewire::test(ChamadosEvolucaoWidget::class)
            ->assertSuccessful();
    }
}
