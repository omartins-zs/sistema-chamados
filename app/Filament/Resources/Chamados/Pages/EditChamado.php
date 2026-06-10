<?php

namespace App\Filament\Resources\Chamados\Pages;

use App\Filament\Resources\Chamados\ChamadoResource;
use App\Models\Chamado;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditChamado extends EditRecord
{
    protected static string $resource = ChamadoResource::class;

    protected static ?string $breadcrumb = 'Editar';

    public function getRecord(): Chamado
    {
        /** @var Chamado $record */
        $record = parent::getRecord();

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Visualizar'),
            DeleteAction::make()
                ->label('Excluir')
                ->visible(fn (): bool => auth()->user()->can('delete', $this->getRecord())),
        ];
    }

    public function getTitle(): string
    {
        return "Editar {$this->getRecord()->protocolo}";
    }
}
