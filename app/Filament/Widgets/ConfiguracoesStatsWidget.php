<?php

namespace App\Filament\Widgets;

use App\Enums\TipoUsuarioEnum;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Models\Setor;
use App\Models\Usuario;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConfiguracoesStatsWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('Chamados', Chamado::query()->count())
                ->description('Total registrado')
                ->icon(Heroicon::OutlinedTicket)
                ->color('primary'),
            Stat::make('Técnicos', Usuario::query()->where('tipo_usuario', TipoUsuarioEnum::TECNICO)->count())
                ->description('Usuários ativos no painel')
                ->icon(Heroicon::OutlinedUsers)
                ->color('info'),
            Stat::make('Setores', Setor::query()->count())
                ->description('Áreas de atendimento')
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->color('warning'),
            Stat::make('Avaliações', AvaliacaoChamado::query()->count())
                ->description('Feedbacks recebidos')
                ->icon(Heroicon::OutlinedStar)
                ->color('success'),
        ];
    }
}
