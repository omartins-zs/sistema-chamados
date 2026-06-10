<?php

namespace App\Http\Controllers\Admin;

use App\Filament\Resources\Chamados\ChamadoResource;
use App\Http\Controllers\Controller;
use App\Models\Chamado;
use App\Services\ChamadoRelatorioPdfService;
use Symfony\Component\HttpFoundation\Response;

class ChamadoRelatorioController extends Controller
{
    public function lista(): Response
    {
        $chamados = ChamadoResource::getEloquentQuery()
            ->orderByDesc('created_at')
            ->get()
            ->ensure(Chamado::class);

        return app(ChamadoRelatorioPdfService::class)->gerarLista($chamados);
    }

    public function individual(Chamado $chamado): Response
    {
        abort_unless(auth()->user()?->can('view', $chamado), 403);

        return app(ChamadoRelatorioPdfService::class)->gerarIndividual($chamado);
    }
}
