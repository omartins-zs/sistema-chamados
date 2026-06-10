<?php

namespace App\Filament\Resources\Chamados\Pages;

use App\Filament\Resources\Chamados\ChamadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChamados extends ListRecords
{
    protected static string $resource = ChamadoResource::class;

    protected static ?string $title = 'Chamados';

    protected static ?string $breadcrumb = '';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Novo Chamado')
                ->visible(fn (): bool => auth()->user()?->can('create', ChamadoResource::getModel()) ?? false),
        ];
    }
}
