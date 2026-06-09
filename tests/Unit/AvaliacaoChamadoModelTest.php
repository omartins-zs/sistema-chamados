<?php

namespace Tests\Unit;

use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvaliacaoChamadoModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_relacionamento_chamado(): void
    {
        $avaliacao = AvaliacaoChamado::factory()->create();

        $this->assertInstanceOf(Chamado::class, $avaliacao->chamado);
    }
}
