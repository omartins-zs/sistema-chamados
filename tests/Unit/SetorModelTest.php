<?php

namespace Tests\Unit;

use App\Models\Chamado;
use App\Models\Setor;
use App\Models\Usuario;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetorModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_relacionamentos_usuarios_e_chamados(): void
    {
        $setor = Setor::query()->firstOrFail();
        Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        Chamado::factory()->create(['setor_id' => $setor->id]);

        $this->assertCount(1, $setor->usuarios);
        $this->assertCount(1, $setor->chamados);
    }

    public function test_cast_ativo(): void
    {
        $setor = Setor::factory()->create(['ativo' => false]);

        $this->assertFalse($setor->ativo);
    }
}
