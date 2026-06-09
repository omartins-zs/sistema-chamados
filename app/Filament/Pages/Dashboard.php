<?php

namespace App\Filament\Pages;

use App\Filament\Support\CriarChamadoAcao;
use App\Filament\Widgets\ChamadosEmAtendimentoWidget;
use App\Filament\Widgets\ChamadosEncerradosWidget;
use App\Filament\Widgets\ResumoGeralChamadosWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Painel';

    protected static ?string $title = 'Painel';

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            ResumoGeralChamadosWidget::class,
            ChamadosEmAtendimentoWidget::class,
            ChamadosEncerradosWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CriarChamadoAcao::make(),
        ];
    }
}
