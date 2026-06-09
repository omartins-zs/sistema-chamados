<?php

namespace App\Filament\Resources\Setores\Pages;

use App\Filament\Resources\Setores\SetorResource;
use App\Services\NotificacaoChamadoService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSetores extends ManageRecords
{
    protected static string $resource = SetorResource::class;

    protected static ?string $title = 'Setores';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Novo Setor')
                ->after(function ($record): void {
                    app(NotificacaoChamadoService::class)->notificarSetorCriado($record);
                }),
        ];
    }
}
