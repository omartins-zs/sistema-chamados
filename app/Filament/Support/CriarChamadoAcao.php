<?php

namespace App\Filament\Support;

use App\Filament\Resources\Chamados\ChamadoResource;
use App\Http\Requests\CriarChamadoRequest;
use App\Models\Chamado;
use App\Services\ChamadoService;
use App\Services\NotificacaoChamadoService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CriarChamadoAcao
{
    public static function make(): Action
    {
        return Action::make('criarChamado')
            ->label('Novo Chamado')
            ->icon(Heroicon::OutlinedPlus)
            ->color('primary')
            ->button()
            ->modalHeading('Abrir novo chamado')
            ->modalDescription('Registre um chamado em nome do solicitante. O protocolo será gerado automaticamente.')
            ->modalSubmitActionLabel('Abrir chamado')
            ->schema(CriarChamadoFormulario::campos())
            ->visible(fn (): bool => auth()->user()?->can('create', Chamado::class) ?? false)
            ->action(function (array $data, Action $action): void {
                $validador = Validator::make(
                    $data,
                    (new CriarChamadoRequest)->rules(),
                    (new CriarChamadoRequest)->messages(),
                );

                if ($validador->fails()) {
                    throw new ValidationException($validador);
                }

                $chamado = app(ChamadoService::class)->criar($validador->validated());

                app(NotificacaoChamadoService::class)->notificarChamadoCriado($chamado);

                Notification::make()
                    ->title('Chamado registrado')
                    ->body("Protocolo {$chamado->protocolo} criado com sucesso.")
                    ->success()
                    ->send();

                $action->redirect(
                    ChamadoResource::getUrl('view', ['record' => $chamado]),
                    navigate: true,
                );
            });
    }
}
