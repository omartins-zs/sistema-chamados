<?php

namespace App\Filament\Resources\HistoricoChamados;

use App\Enums\StatusChamadoEnum;
use App\Filament\Resources\HistoricoChamados\Pages\ManageHistoricos;
use App\Models\HistoricoChamado;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HistoricoChamadoResource extends Resource
{
    protected static ?string $model = HistoricoChamado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Históricos';

    protected static ?string $modelLabel = 'Histórico';

    protected static ?string $pluralModelLabel = 'Históricos';

    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('chamado.protocolo')->label('Protocolo'),
                TextEntry::make('tecnico.nome')->label('Técnico'),
                TextEntry::make('tecnico.setor.nome')->label('Setor'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->rotulo())
                    ->color(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->cor()),
                TextEntry::make('descricao')->label('Descrição')->columnSpanFull(),
                IconEntry::make('visivel_solicitante')->label('Visível ao solicitante')->boolean(),
                TextEntry::make('created_at')->label('Data')->dateTime('d/m/Y H:i'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('chamado.protocolo')->label('Protocolo')->searchable()->sortable(),
                TextColumn::make('tecnico.nome')->label('Técnico')->searchable(),
                TextColumn::make('tecnico.setor.nome')->label('Setor'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->rotulo())
                    ->color(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->cor()),
                TextColumn::make('descricao')->label('Descrição')->limit(60),
                IconColumn::make('visivel_solicitante')->label('Público')->boolean(),
                TextColumn::make('created_at')->label('Data')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()->label('Visualizar'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $usuario = auth()->user();

        if ($usuario && ! $usuario->ehAdministrador()) {
            $query->whereHas('chamado', fn (Builder $builder) => $builder->where('setor_id', $usuario->setor_id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHistoricos::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
