<?php

namespace App\Filament\Widgets;

use App\Enums\StatusChamadoEnum;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class ResumoChamadosOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $query = $this->getChamadoQuery();

        return [
            Stat::make('Total de Chamados', (clone $query)->count()),
            Stat::make('Chamados em Aberto', (clone $query)->where('status', StatusChamadoEnum::EM_ABERTO)->count()),
            Stat::make('Chamados Acessados', (clone $query)->where('status', StatusChamadoEnum::ACESSADO)->count()),
            Stat::make('Chamados em Andamento', (clone $query)->where('status', StatusChamadoEnum::EM_ANDAMENTO)->count()),
            Stat::make('Chamados Aguardando Cliente', (clone $query)->where('status', StatusChamadoEnum::AGUARDANDO_CLIENTE)->count()),
            Stat::make('Chamados Aguardando Terceiros', (clone $query)->where('status', StatusChamadoEnum::AGUARDANDO_TERCEIROS)->count()),
            Stat::make('Chamados Pausados', (clone $query)->where('status', StatusChamadoEnum::PAUSADO)->count()),
            Stat::make('Chamados Concluídos', (clone $query)->where('status', StatusChamadoEnum::CONCLUIDO)->count()),
            Stat::make('Chamados Finalizados', (clone $query)->where('status', StatusChamadoEnum::FINALIZADO)->count()),
            Stat::make('Chamados Cancelados', (clone $query)->where('status', StatusChamadoEnum::CANCELADO)->count()),
            Stat::make('Média de Satisfação', number_format((float) AvaliacaoChamado::query()->avg('nota_satisfacao'), 1).' / 5'),
            Stat::make('Média de Tempo de Resolução', number_format((float) AvaliacaoChamado::query()->avg('nota_tempo_resolucao'), 1).' / 5'),
        ];
    }

    private function getChamadoQuery(): Builder
    {
        $query = Chamado::query();
        $usuario = auth()->user();

        if ($usuario && ! $usuario->ehAdministrador()) {
            $query->where('setor_id', $usuario->setor_id);
        }

        return $query;
    }
}
