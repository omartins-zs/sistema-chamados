<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\FiltraChamadosPorUsuario;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ChamadosEvolucaoWidget extends ChartWidget
{
    use FiltraChamadosPorUsuario;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Evolução de Chamados';

    protected ?string $description = 'Novos chamados nos últimos 6 meses';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $meses = collect(range(5, 0))->map(fn (int $offset): Carbon => now()->startOfMonth()->subMonths($offset));

        $contagens = $meses->map(function (Carbon $mes): int {
            return (clone $this->getChamadoQuery())
                ->whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Chamados abertos',
                    'data' => $contagens->all(),
                    'borderColor' => '#00468a',
                    'backgroundColor' => 'rgba(0, 70, 138, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $meses->map(fn (Carbon $mes): string => $mes->translatedFormat('M/Y'))->all(),
        ];
    }
}
