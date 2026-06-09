<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\FiltraChamadosPorUsuario;
use App\Models\AvaliacaoChamado;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResumoGeralChamadosWidget extends StatsOverviewWidget
{
    use FiltraChamadosPorUsuario;

    protected static ?int $sort = 1;

    protected ?string $heading = 'Resumo Geral';

    protected ?string $description = 'Volume total e indicadores de satisfação';

    protected function getStats(): array
    {
        $query = $this->getChamadoQuery();

        return [
            Stat::make('Total de Chamados', (clone $query)->count())
                ->description('Registrados no sistema')
                ->icon(Heroicon::OutlinedTicket)
                ->color('primary'),
            Stat::make('Média de Satisfação', number_format((float) AvaliacaoChamado::query()->avg('nota_satisfacao'), 1).' / 5')
                ->description('Feedback dos solicitantes')
                ->icon(Heroicon::OutlinedStar)
                ->color('success'),
            Stat::make('Média de Tempo de Resolução', number_format((float) AvaliacaoChamado::query()->avg('nota_tempo_resolucao'), 1).' / 5')
                ->description('Percepção de agilidade')
                ->icon(Heroicon::OutlinedClock)
                ->color('info'),
        ];
    }
}
