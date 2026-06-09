<?php

namespace Tests\Unit;

use App\Exceptions\AvaliacaoChamadoException;
use App\Exceptions\ChamadoNaoPertenceAoSetorException;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    public function test_avaliacao_chamado_exception(): void
    {
        $exception = new AvaliacaoChamadoException('Link de avaliação inválido.');

        $this->assertSame('Link de avaliação inválido.', $exception->getMessage());
    }

    public function test_chamado_nao_pertence_ao_setor_exception(): void
    {
        $exception = new ChamadoNaoPertenceAoSetorException('Setor inválido.');

        $this->assertSame('Setor inválido.', $exception->getMessage());
    }
}
