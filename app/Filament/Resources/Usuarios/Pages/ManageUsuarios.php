<?php

namespace App\Filament\Resources\Usuarios\Pages;

use App\Filament\Resources\Usuarios\UsuarioResource;
use App\Services\NotificacaoChamadoService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUsuarios extends ManageRecords
{
    protected static string $resource = UsuarioResource::class;

    protected static ?string $title = 'Técnicos';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Novo Técnico')
                ->after(function ($record): void {
                    app(NotificacaoChamadoService::class)->notificarTecnicoCriado($record);
                }),
        ];
    }
}
