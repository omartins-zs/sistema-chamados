<?php

namespace App\Http\Controllers;

use App\Exceptions\AvaliacaoChamadoException;
use App\Http\Requests\CriarAvaliacaoRequest;
use App\Services\AvaliacaoChamadoService;
use App\Services\ChamadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AvaliacaoPublicaController extends Controller
{
    public function __construct(
        private readonly ChamadoService $chamadoService,
        private readonly AvaliacaoChamadoService $avaliacaoChamadoService,
    ) {}

    public function exibir(string $protocolo, string $token): View|RedirectResponse
    {
        $chamado = $this->chamadoService->buscarPorProtocolo($protocolo);

        if ($chamado === null) {
            return redirect()
                ->route('chamados.criar')
                ->with('erro', 'Chamado não encontrado.');
        }

        try {
            $this->avaliacaoChamadoService->validarToken($chamado, $token);
            $this->avaliacaoChamadoService->validarChamadoParaAvaliacao($chamado);
        } catch (AvaliacaoChamadoException $exception) {
            return redirect()
                ->route('chamados.criar')
                ->with('erro', $exception->getMessage());
        }

        return view('publico.chamados.avaliar', compact('chamado', 'token'));
    }

    public function salvar(CriarAvaliacaoRequest $request, string $protocolo, string $token): RedirectResponse
    {
        $chamado = $this->chamadoService->buscarPorProtocolo($protocolo);

        if ($chamado === null) {
            return redirect()
                ->route('chamados.criar')
                ->with('erro', 'Chamado não encontrado.');
        }

        try {
            $this->avaliacaoChamadoService->criar($chamado, $token, $request->validated());
        } catch (AvaliacaoChamadoException $exception) {
            return redirect()
                ->route('chamados.criar')
                ->with('erro', $exception->getMessage());
        }

        return redirect()
            ->route('chamados.avaliar', ['protocolo' => $protocolo, 'token' => $token])
            ->with('sucesso', 'Obrigado! Sua avaliação foi registrada com sucesso.');
    }
}
