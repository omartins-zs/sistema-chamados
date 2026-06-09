<?php

namespace Tests\Feature;

use App\Enums\StatusChamadoEnum;
use App\Models\Chamado;
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

        $this->assertSame(3, Chamado::query()->count());
        $this->assertTrue(
            Chamado::query()->where('status', StatusChamadoEnum::EM_ABERTO)->exists()
        );
        $this->assertTrue(
            Chamado::query()->where('status', StatusChamadoEnum::EM_ANDAMENTO)->exists()
        );
    }
}
