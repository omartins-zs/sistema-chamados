<?php

namespace Tests\Unit;

use App\Models\Chamado;
use App\Services\ChamadoRelatorioPdfService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChamadoRelatorioPdfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_gera_pdf_individual(): void
    {
        $chamado = Chamado::factory()->create();

        $response = app(ChamadoRelatorioPdfService::class)->gerarIndividual($chamado);

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_gera_pdf_da_lista(): void
    {
        $chamados = Chamado::factory()->count(3)->create();

        $response = app(ChamadoRelatorioPdfService::class)->gerarLista($chamados);

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
    }
}
