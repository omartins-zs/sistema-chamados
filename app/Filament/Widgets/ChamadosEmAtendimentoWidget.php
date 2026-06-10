<?php

namespace App\Filament\Widgets;

use App\Enums\StatusChamadoEnum;
use App\Filament\Widgets\Concerns\FiltraChamadosPorUsuario;
use Filament\Support\Icons\Heroicon;
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
            Stat::make('Em Aberto', (clone $query)->where('status', StatusChamadoEnum::EM_ABERTO)->count())
                ->icon(Heroicon::OutlinedInbox)
                ->color('gray'),
            Stat::make('Acessados', (clone $query)->where('status', StatusChamadoEnum::ACESSADO)->count())
                ->icon(Heroicon::OutlinedEye)
                ->color('info'),
            Stat::make('Em Andamento', (clone $query)->where('status', StatusChamadoEnum::EM_ANDAMENTO)->count())
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning'),
            Stat::make('Aguardando Cliente', (clone $query)->where('status', StatusChamadoEnum::AGUARDANDO_CLIENTE)->count())
                ->icon(Heroicon::OutlinedUser)
                ->color('primary'),
            Stat::make('Aguardando Terceiros', (clone $query)->where('status', StatusChamadoEnum::AGUARDANDO_TERCEIROS)->count())
                ->icon(Heroicon::OutlinedBuildingOffice)
                ->color('gray'),
            Stat::make('Pausados', (clone $query)->where('status', StatusChamadoEnum::PAUSADO)->count())
                ->icon(Heroicon::OutlinedPause)
                ->color('gray'),
            Stat::make('Bloqueados', (clone $query)->where('status', StatusChamadoEnum::BLOQUEADO)->count())
                ->icon(Heroicon::OutlinedNoSymbol)
                ->color('danger'),
        ];
    }
}
