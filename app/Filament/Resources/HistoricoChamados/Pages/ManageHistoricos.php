<?php

namespace App\Filament\Resources\HistoricoChamados\Pages;

use App\Filament\Resources\HistoricoChamados\HistoricoChamadoResource;
use Filament\Resources\Pages\ManageRecords;

class ManageHistoricos extends ManageRecords
{
    protected static string $resource = HistoricoChamadoResource::class;

    protected static ?string $title = 'Históricos';
}
