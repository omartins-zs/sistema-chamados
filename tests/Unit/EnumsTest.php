<?php

namespace Tests\Unit;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Enums\TipoUsuarioEnum;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    public function test_status_chamado_rotulos(): void
    {
        $this->assertSame('Em Aberto', StatusChamadoEnum::EM_ABERTO->rotulo());
        $this->assertSame('Finalizado', StatusChamadoEnum::FINALIZADO->rotulo());
        $this->assertSame('Aguardando Cliente', StatusChamadoEnum::normalizar('aguardando_cliente')->rotulo());
        $this->assertCount(9, StatusChamadoEnum::opcoes());
    }

    public function test_complexidade_rotulos(): void
    {
        $this->assertSame('Crítica', ComplexidadeChamadoEnum::CRITICA->rotulo());
        $this->assertCount(4, ComplexidadeChamadoEnum::opcoes());
    }

    public function test_tipo_usuario_rotulos(): void
    {
        $this->assertSame('Administrador', TipoUsuarioEnum::ADMINISTRADOR->rotulo());
        $this->assertSame('Técnico', TipoUsuarioEnum::TECNICO->rotulo());
    }
}
