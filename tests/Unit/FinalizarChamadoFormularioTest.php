<?php

namespace Tests\Unit;

use App\Filament\Support\FinalizarChamadoFormulario;
use Tests\TestCase;

class FinalizarChamadoFormularioTest extends TestCase
{
    public function test_campos_retorna_motivo_e_descricao(): void
    {
        $campos = FinalizarChamadoFormulario::campos();

        $this->assertCount(2, $campos);
        $this->assertSame('motivo', $campos[0]->getName());
        $this->assertSame('descricao', $campos[1]->getName());
    }
}
