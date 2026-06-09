<?php

namespace App\Filament\Resources\AvaliacaoChamados\Pages;

use App\Filament\Resources\AvaliacaoChamados\AvaliacaoChamadoResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAvaliacoes extends ManageRecords
{
    protected static string $resource = AvaliacaoChamadoResource::class;

    protected static ?string $title = 'Avaliações';
}
