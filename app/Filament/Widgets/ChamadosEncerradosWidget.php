<?php

namespace App\Filament\Widgets;

use App\Enums\StatusChamadoEnum;
use App\Filament\Widgets\Concerns\FiltraChamadosPorUsuario;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChamadosEncerradosWidget extends StatsOverviewWidget
{
    use FiltraChamadosPorUsuario;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Chamados Encerrados';

    protected ?string $description = 'Atendimentos concluídos, finalizados ou cancelados';

    protected function getStats(): array
    {
        $query = $this->getChamadoQuery();

        return [
            Stat::make('Concluídos', (clone $query)->where('status', StatusChamadoEnum::CONCLUIDO)->count())
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Finalizados', (clone $query)->where('status', StatusChamadoEnum::FINALIZADO)->count())
                ->icon(Heroicon::OutlinedCheckBadge)
                ->color('primary'),
            Stat::make('Cancelados', (clone $query)->where('status', StatusChamadoEnum::CANCELADO)->count())
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger'),
        ];
    }
}
