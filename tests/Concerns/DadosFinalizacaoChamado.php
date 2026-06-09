<?php

namespace Tests\Concerns;

use App\Models\Chamado;
use App\Models\Usuario;

trait DadosFinalizacaoChamado
{
    /**
     * @return array{motivo: string, descricao: string}
     */
    protected function dadosFinalizacaoChamado(): array
    {
        return [
            'motivo' => 'Problema resolvido',
            'descricao' => 'Atendimento concluído com sucesso após verificação e correção do equipamento.',
        ];
    }

    protected function tecnicoDoChamado(Chamado $chamado): Usuario
    {
        return Usuario::factory()->tecnico()->create([
            'setor_id' => $chamado->setor_id,
        ]);
    }
}
