<?php

namespace Tests\Feature;

use App\Enums\StatusChamadoEnum;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use Database\Seeders\ChamadoSeeder;
use Database\Seeders\SetorSeeder;
use Database\Seeders\UsuarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChamadoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_cria_chamados_de_demonstracao(): void
    {
        $this->seed(SetorSeeder::class);
        $this->seed(UsuarioSeeder::class);
        $this->seed(ChamadoSeeder::class);

        $this->assertGreaterThanOrEqual(60, Chamado::query()->count());
        $this->assertGreaterThan(0, HistoricoChamado::query()->count());
        $this->assertGreaterThan(0, AvaliacaoChamado::query()->count());

        foreach (StatusChamadoEnum::cases() as $status) {
            $this->assertTrue(
                Chamado::query()->where('status', $status)->exists(),
                "Nenhum chamado com status {$status->value} foi criado pelo seeder."
            );
        }

        $this->assertTrue(
            Chamado::query()->where('protocolo', 'CHM-'.now()->year.'-000002')->exists()
        );
    }
}
