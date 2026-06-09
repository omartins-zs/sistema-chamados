<?php

namespace App\Services;

use App\Enums\StatusChamadoEnum;
use App\Exceptions\AvaliacaoChamadoException;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;

class AvaliacaoChamadoService
{
    public function __construct(
        private readonly NotificacaoChamadoService $notificacaoChamadoService,
    ) {}

    public function validarToken(Chamado $chamado, string $token): void
    {
        if ($chamado->token_avaliacao !== $token) {
            throw new AvaliacaoChamadoException('Link de avaliação inválido.');
        }

        if ($chamado->expira_token_avaliacao_em !== null && $chamado->expira_token_avaliacao_em->isPast()) {
            throw new AvaliacaoChamadoException('O link de avaliação expirou.');
        }
    }

    public function validarChamadoParaAvaliacao(Chamado $chamado): void
    {
        if ($chamado->status !== StatusChamadoEnum::FINALIZADO) {
            throw new AvaliacaoChamadoException('Este chamado ainda não foi finalizado.');
        }

        if ($chamado->avaliacao()->exists()) {
            throw new AvaliacaoChamadoException('Este chamado já foi avaliado.');
        }
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function criar(Chamado $chamado, string $token, array $dados): AvaliacaoChamado
    {
        $this->validarToken($chamado, $token);
        $this->validarChamadoParaAvaliacao($chamado);

        $avaliacao = AvaliacaoChamado::query()->create([
            'chamado_id' => $chamado->id,
            'nota_satisfacao' => $dados['nota_satisfacao'],
            'nota_tempo_resolucao' => $dados['nota_tempo_resolucao'],
            'comentario' => $dados['comentario'] ?? null,
        ]);

        $this->notificacaoChamadoService->notificarAvaliacaoRegistrada($avaliacao->load('chamado'));

        return $avaliacao;
    }
}
