<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class FinalizarChamadoFormulario
{
    /**
     * @return array<int, TextInput|Textarea>
     */
    public static function campos(): array
    {
        return [
            TextInput::make('motivo')
                ->label('Motivo da finalização')
                ->required()
                ->maxLength(255)
                ->placeholder('Ex.: Problema resolvido, equipamento substituído'),
            Textarea::make('descricao')
                ->label('Texto da finalização')
                ->required()
                ->minLength(10)
                ->maxLength(5000)
                ->rows(5)
                ->placeholder('Descreva o que foi feito e o resultado do atendimento.'),
        ];
    }
}
