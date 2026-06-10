<?php

namespace App\Services;

use App\Models\Chamado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ChamadoRelatorioPdfService
{
    public function gerarIndividual(Chamado $chamado): Response
    {
        $chamado->load(['setor', 'tecnicoResponsavel', 'historicos.tecnico.setor', 'avaliacao']);

        $pdf = Pdf::loadView('pdf.chamado-individual', [
            'chamado' => $chamado,
            'historicos' => $chamado->historicos,
        ]);

        return $pdf->download($this->nomeArquivo($chamado->protocolo));
    }

    /**
     * @param  Collection<int, Chamado>  $chamados
     */
    public function gerarLista(Collection $chamados): Response
    {
        $chamados->load(['setor', 'tecnicoResponsavel']);

        foreach ($chamados as $chamado) {
            $this->normalizarTextosDoChamado($chamado);
        }

        $pdf = Pdf::loadView('pdf.chamados-lista', [
            'chamados' => $chamados,
            'geradoEm' => now(),
        ])->setOption('isUnicode', true);

        return $pdf->download('relatorio-chamados-'.now()->format('Y-m-d-His').'.pdf');
    }

    private function normalizarTextosDoChamado(Chamado $chamado): void
    {
        $chamado->protocolo = $this->sanitizarUtf8($chamado->protocolo);
        $chamado->nome_solicitante = $this->sanitizarUtf8($chamado->nome_solicitante);
        $chamado->titulo = $this->sanitizarUtf8($chamado->titulo);
        $chamado->setor->nome = $this->sanitizarUtf8($chamado->setor->nome);
    }

    private function sanitizarUtf8(?string $valor): string
    {
        if ($valor === null || $valor === '') {
            return '';
        }

        $texto = iconv('UTF-8', 'UTF-8//IGNORE', $valor);

        return $texto === false ? '' : $texto;
    }

    private function nomeArquivo(string $protocolo): string
    {
        return 'chamado-'.Str::slug($protocolo).'.pdf';
    }
}
