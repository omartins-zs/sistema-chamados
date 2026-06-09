<?php

namespace Tests\Unit;

use App\Filament\Support\CriarChamadoFormulario;
use Tests\TestCase;

class CriarChamadoFormularioTest extends TestCase
{
    public function test_campos_retorna_formulario_completo(): void
    {
        $campos = CriarChamadoFormulario::campos();

        $this->assertCount(7, $campos);
        $this->assertSame('nome_solicitante', $campos[0]->getName());
        $this->assertSame('setor_id', $campos[6]->getName());
    }
}
