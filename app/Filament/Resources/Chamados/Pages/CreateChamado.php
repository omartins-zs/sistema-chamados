<?php

namespace App\Filament\Resources\Chamados\Pages;

use App\Filament\Resources\Chamados\ChamadoResource;
use App\Filament\Support\ChamadoFormulario;
use App\Http\Requests\CriarChamadoRequest;
use App\Models\Chamado;
use App\Services\ChamadoService;
use App\Services\NotificacaoChamadoService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateChamado extends CreateRecord
{
    protected static string $resource = ChamadoResource::class;

    protected static ?string $title = 'Novo Chamado';

    protected static ?string $breadcrumb = 'Novo';

    public function form(Schema $schema): Schema
    {
        return $schema->components(ChamadoFormulario::camposCriacao());
    }

    protected function handleRecordCreation(array $data): Model
    {
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

        return $chamado;
    }

    protected function getRedirectUrl(): string
    {
        /** @var Chamado $record */
        $record = $this->getRecord();

        return ChamadoResource::getUrl('view', ['record' => $record]);
    }
}
