<?php

namespace App\Filament\Exports;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Models\Chamado;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ChamadoExporter extends Exporter
{
    protected static ?string $model = Chamado::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('protocolo')->label('Protocolo'),
            ExportColumn::make('nome_solicitante')->label('Solicitante'),
            ExportColumn::make('email_solicitante')->label('E-mail'),
            ExportColumn::make('telefone_solicitante')->label('Telefone'),
            ExportColumn::make('titulo')->label('Título'),
            ExportColumn::make('descricao')->label('Descrição'),
            ExportColumn::make('complexidade')
                ->label('Complexidade')
                ->formatStateUsing(fn (ComplexidadeChamadoEnum|string $state): string => ComplexidadeChamadoEnum::normalizar($state)->rotulo()),
            ExportColumn::make('setor.nome')->label('Setor'),
            ExportColumn::make('tecnicoResponsavel.nome')->label('Técnico'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->rotulo()),
            ExportColumn::make('created_at')
                ->label('Abertura')
                ->formatStateUsing(fn (?string $state): ?string => $state),
            ExportColumn::make('finalizado_em')
                ->label('Finalização')
                ->formatStateUsing(fn (?string $state): ?string => $state),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação de chamados foi concluída com '.Number::format($export->successful_rows).' registro(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' registro(s) falharam.';
        }

        return $body;
    }
}
