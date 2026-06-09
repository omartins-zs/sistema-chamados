<?php

namespace App\Filament\Support;

use App\Enums\ComplexidadeChamadoEnum;
use App\Models\Setor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class CriarChamadoFormulario
{
    /**
     * @return array<int, TextInput|Textarea|Select>
     */
    public static function campos(): array
    {
        return [
            TextInput::make('nome_solicitante')
                ->label('Nome do solicitante')
                ->required()
                ->maxLength(255),
            TextInput::make('email_solicitante')
                ->label('E-mail')
                ->email()
                ->required()
                ->maxLength(255),
            TextInput::make('telefone_solicitante')
                ->label('Telefone / WhatsApp')
                ->required()
                ->maxLength(20),
            TextInput::make('titulo')
                ->label('Título do chamado')
                ->required()
                ->maxLength(255),
            Textarea::make('descricao')
                ->label('Descrição detalhada')
                ->required()
                ->minLength(10)
                ->rows(5),
            Select::make('complexidade')
                ->label('Complexidade')
                ->options(ComplexidadeChamadoEnum::opcoes())
                ->required(),
            Select::make('setor_id')
                ->label('Setor responsável')
                ->options(fn (): array => Setor::query()
                    ->where('ativo', true)
                    ->orderBy('nome')
                    ->pluck('nome', 'id')
                    ->all())
                ->searchable()
                ->required(),
        ];
    }
}
