<?php

namespace App\Filament\Support;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Models\Setor;
use App\Models\Usuario;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ChamadoFormulario
{
    /**
     * @return array<int, TextInput|Textarea|Select>
     */
    public static function camposCriacao(): array
    {
        return CriarChamadoFormulario::campos();
    }

    /**
     * @return array<int, TextInput|Textarea|Select>
     */
    public static function camposEdicao(?Usuario $usuario = null): array
    {
        $usuario ??= auth()->user();

        if ($usuario?->ehAdministrador()) {
            return [
                ...self::camposSolicitante(),
                ...self::camposChamado(),
                ...self::camposAtribuicao(),
            ];
        }

        return self::camposAtribuicao();
    }

    /**
     * @return array<int, TextInput|Textarea|Select>
     */
    private static function camposSolicitante(): array
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
        ];
    }

    /**
     * @return array<int, TextInput|Textarea|Select>
     */
    private static function camposChamado(): array
    {
        return [
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

    /**
     * @return array<int, Select>
     */
    private static function camposAtribuicao(): array
    {
        return [
            Select::make('status')
                ->label('Status')
                ->options(StatusChamadoEnum::opcoes())
                ->required(),
            Select::make('tecnico_responsavel_id')
                ->label('Técnico Responsável')
                ->relationship('tecnicoResponsavel', 'nome')
                ->searchable()
                ->preload(),
        ];
    }
}
