<?php

namespace App\Services;

use App\Enums\StatusChamadoEnum;
use App\Exceptions\ChamadoNaoPertenceAoSetorException;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class HistoricoChamadoService
{
    public function __construct(
        private readonly NotificacaoChamadoService $notificacaoChamadoService,
    ) {}

    /**
     * @param  array<string, mixed>  $dados
     */
    public function adicionar(Chamado $chamado, Usuario $tecnico, array $dados): HistoricoChamado
    {
        $this->validarSetorDoTecnico($chamado, $tecnico);

        return DB::transaction(function () use ($chamado, $tecnico, $dados): HistoricoChamado {
            $status = StatusChamadoEnum::from($dados['status']);
            $assumiu = $this->assumirChamadoSeNecessario($chamado, $tecnico, $status);

            $historico = HistoricoChamado::query()->create([
                'chamado_id' => $chamado->id,
                'tecnico_id' => $tecnico->id,
                'status' => $status,
                'descricao' => $dados['descricao'],
                'visivel_solicitante' => $dados['visivel_solicitante'] ?? false,
            ]);

            $chamado->update(['status' => $status]);

            if ($assumiu) {
                $this->notificacaoChamadoService->notificarChamadoAssumido($chamado->refresh(), $tecnico);
            }

            $this->notificacaoChamadoService->notificarHistoricoAdicionado($historico->load(['tecnico', 'chamado']));
            $this->notificacaoChamadoService->notificarStatusAlterado($chamado->refresh(), $status);

            return $historico;
        });
    }

    public function registrarFinalizacao(
        Chamado $chamado,
        Usuario $tecnico,
        string $motivo,
        string $descricao,
    ): HistoricoChamado {
        $this->validarSetorDoTecnico($chamado, $tecnico);

        return HistoricoChamado::query()->create([
            'chamado_id' => $chamado->id,
            'tecnico_id' => $tecnico->id,
            'status' => StatusChamadoEnum::FINALIZADO,
            'descricao' => "Motivo da finalização: {$motivo}\n\n{$descricao}",
            'visivel_solicitante' => true,
        ]);
    }

    public function assumirChamado(Chamado $chamado, Usuario $tecnico): Chamado
    {
        $this->validarSetorDoTecnico($chamado, $tecnico);

        if ($chamado->tecnico_responsavel_id === null) {
            $chamado->update([
                'tecnico_responsavel_id' => $tecnico->id,
                'status' => StatusChamadoEnum::ACESSADO,
            ]);

            $this->notificacaoChamadoService->notificarChamadoAssumido($chamado->refresh(), $tecnico);
        }

        return $chamado->refresh();
    }

    private function assumirChamadoSeNecessario(
        Chamado $chamado,
        Usuario $tecnico,
        StatusChamadoEnum $status,
    ): bool {
        if ($chamado->tecnico_responsavel_id !== null) {
            return false;
        }

        $chamado->update([
            'tecnico_responsavel_id' => $tecnico->id,
            'status' => in_array($status, [StatusChamadoEnum::EM_ABERTO, StatusChamadoEnum::ACESSADO], true)
                ? StatusChamadoEnum::ACESSADO
                : $status,
        ]);

        return true;
    }

    public function validarSetorDoTecnico(Chamado $chamado, Usuario $tecnico): void
    {
        if ($tecnico->ehAdministrador()) {
            return;
        }

        if ($tecnico->setor_id !== $chamado->setor_id) {
            throw new ChamadoNaoPertenceAoSetorException(
                'O técnico não pode atuar em chamados de outro setor.'
            );
        }
    }
}
