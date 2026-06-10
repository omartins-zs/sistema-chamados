<?php

namespace Tests\Unit;

use App\Filament\Exports\ChamadoExporter;
use Tests\TestCase;

class ChamadoExporterTest extends TestCase
{
    public function test_exporter_define_colunas_de_chamados(): void
    {
        $colunas = ChamadoExporter::getColumns();

        $this->assertNotEmpty($colunas);
        $this->assertSame('protocolo', $colunas[0]->getName());
    }
}
