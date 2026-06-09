<?php

namespace App\Filament\Resources\AvaliacaoChamados;

use App\Filament\Resources\AvaliacaoChamados\Pages\ManageAvaliacoes;
use App\Models\AvaliacaoChamado;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AvaliacaoChamadoResource extends Resource
{
    protected static ?string $model = AvaliacaoChamado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $navigationLabel = 'Avaliações';

    protected static ?string $modelLabel = 'Avaliação';

    protected static ?string $pluralModelLabel = 'Avaliações';

    protected static ?int $navigationSort = 3;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('chamado.protocolo')->label('Protocolo'),
                TextEntry::make('chamado.nome_solicitante')->label('Solicitante'),
                TextEntry::make('nota_satisfacao')->label('Satisfação'),
                TextEntry::make('nota_tempo_resolucao')->label('Tempo de Resolução'),
                TextEntry::make('comentario')->label('Comentário')->columnSpanFull(),
                TextEntry::make('created_at')->label('Data')->dateTime('d/m/Y H:i'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('chamado.protocolo')->label('Protocolo')->searchable()->sortable(),
                TextColumn::make('chamado.nome_solicitante')->label('Solicitante')->searchable(),
                TextColumn::make('nota_satisfacao')->label('Satisfação')->sortable(),
                TextColumn::make('nota_tempo_resolucao')->label('Tempo')->sortable(),
                TextColumn::make('comentario')->label('Comentário')->limit(50),
                TextColumn::make('created_at')->label('Data')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()->label('Visualizar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAvaliacoes::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
