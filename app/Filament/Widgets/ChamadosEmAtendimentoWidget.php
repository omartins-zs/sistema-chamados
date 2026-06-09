<?php

namespace App\Filament\Widgets;

use App\Enums\StatusChamadoEnum;
use App\Filament\Widgets\Concerns\FiltraChamadosPorUsuario;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChamadosEmAtendimentoWidget extends StatsOverviewWidget
{
    use FiltraChamadosPorUsuario;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Chamados em Atendimento';

    protected ?string $description = 'Status ativos — aguardando ação da equipe ou do solicitante';

    protected function getStats(): array
    {
        $query = $this->getChamadoQuery();

        return [
            Stat::make('Em Aberto', (clone $query)->where('status', StatusChamadoEnum::EM_ABERTO)->count()),
            Stat::make('Acessados', (clone $query)->where('status', StatusChamadoEnum::ACESSADO)->count()),
            Stat::make('Em Andamento', (clone $query)->where('status', StatusChamadoEnum::EM_ANDAMENTO)->count()),
            Stat::make('Aguardando Cliente', (clone $query)->where('status', StatusChamadoEnum::AGUARDANDO_CLIENTE)->count()),
            Stat::make('Aguardando Terceiros', (clone $query)->where('status', StatusChamadoEnum::AGUARDANDO_TERCEIROS)->count()),
            Stat::make('Pausados', (clone $query)->where('status', StatusChamadoEnum::PAUSADO)->count()),
        ];
    }
}
