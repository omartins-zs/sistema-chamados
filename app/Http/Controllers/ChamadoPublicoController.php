<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultarChamadoRequest;
use App\Http\Requests\CriarChamadoRequest;
use App\Models\Setor;
use App\Services\ChamadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChamadoPublicoController extends Controller
{
    public function __construct(
        private readonly ChamadoService $chamadoService,
    ) {}

    public function inicio(): View
    {
        return view('publico.home');
    }

    public function criar(): View
    {
        $setores = Setor::query()->where('ativo', true)->orderBy('nome')->get();

        return view('publico.chamados.criar', compact('setores'));
    }

    public function salvar(CriarChamadoRequest $request): RedirectResponse
    {
        $chamado = $this->chamadoService->criar($request->validated());

        return redirect()
            ->route('chamados.sucesso', $chamado->protocolo)
            ->with('sucesso', "Chamado criado com sucesso! Seu protocolo é {$chamado->protocolo}.");
    }

    public function sucesso(string $protocolo): View|RedirectResponse
    {
        $chamado = $this->chamadoService->buscarPorProtocolo($protocolo);

        if ($chamado === null) {
            return redirect()
                ->route('chamados.criar')
                ->with('erro', 'Chamado não encontrado.');
        }

        return view('publico.chamados.sucesso', compact('chamado'));
    }

    public function consultar(): View
    {
        return view('publico.chamados.consultar');
    }

    public function consultarResultado(ConsultarChamadoRequest $request): View|RedirectResponse
    {
        $chamado = $this->chamadoService->buscarPorProtocolo($request->validated('protocolo'));

        if ($chamado === null) {
            return redirect()
                ->route('chamados.consultar')
                ->withInput()
                ->with('erro', 'Chamado não encontrado. Verifique o protocolo informado.');
        }

        if ($chamado->estaFinalizado()) {
            return redirect()->route('chamados.finalizado', $chamado->protocolo);
        }

        return view('publico.chamados.consultar-resultado', compact('chamado'));
    }

    public function finalizado(string $protocolo): View|RedirectResponse
    {
        $chamado = $this->chamadoService->buscarPorProtocolo($protocolo);

        if ($chamado === null) {
            return redirect()
                ->route('chamados.criar')
                ->with('erro', 'Chamado não encontrado.');
        }

        if (! $chamado->estaFinalizado()) {
            return redirect()
                ->route('chamados.criar')
                ->with('erro', 'Este chamado ainda não foi finalizado.');
        }

        return view('publico.chamados.finalizado', compact('chamado'));
    }
}
