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
        $this->assertSame('Bloqueado', StatusChamadoEnum::BLOQUEADO->rotulo());
        $this->assertCount(10, StatusChamadoEnum::opcoes());
        $this->assertSame('danger', StatusChamadoEnum::BLOQUEADO->cor());
        $this->assertStringContainsString('red', StatusChamadoEnum::BLOQUEADO->classesBadge());
        $this->assertSame(StatusChamadoEnum::EM_ABERTO, StatusChamadoEnum::normalizar(StatusChamadoEnum::EM_ABERTO));
    }

    public function test_complexidade_rotulos(): void
    {
        $this->assertSame('Crítica', ComplexidadeChamadoEnum::CRITICA->rotulo());
        $this->assertCount(4, ComplexidadeChamadoEnum::opcoes());
        $this->assertSame('danger', ComplexidadeChamadoEnum::CRITICA->cor());
        $this->assertStringContainsString('red', ComplexidadeChamadoEnum::CRITICA->classesBadge());
        $this->assertSame(ComplexidadeChamadoEnum::MEDIA, ComplexidadeChamadoEnum::normalizar('media'));
    }

    public function test_tipo_usuario_rotulos(): void
    {
        $this->assertSame('Administrador', TipoUsuarioEnum::ADMINISTRADOR->rotulo());
        $this->assertSame('Técnico', TipoUsuarioEnum::TECNICO->rotulo());
        $this->assertCount(2, TipoUsuarioEnum::opcoes());
    }
}
