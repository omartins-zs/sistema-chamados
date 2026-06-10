<?php

namespace App\Filament\Resources\Chamados;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Filament\Exports\ChamadoExporter;
use App\Filament\Resources\Chamados\Pages\CreateChamado;
use App\Filament\Resources\Chamados\Pages\EditChamado;
use App\Filament\Resources\Chamados\Pages\ListChamados;
use App\Filament\Resources\Chamados\Pages\ViewChamado;
use App\Filament\Support\ChamadoFormulario;
use App\Filament\Support\FinalizarChamadoFormulario;
use App\Models\Chamado;
use App\Models\Usuario;
use App\Services\ChamadoService;
use App\Services\HistoricoChamadoService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChamadoResource extends Resource
{
    protected static ?string $model = Chamado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Chamados';

    protected static ?string $modelLabel = 'Chamado';

    protected static ?string $pluralModelLabel = 'Chamados';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'protocolo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ChamadoFormulario::camposEdicao());
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('protocolo')->label('Protocolo'),
                TextEntry::make('nome_solicitante')->label('Solicitante'),
                TextEntry::make('email_solicitante')->label('E-mail'),
                TextEntry::make('telefone_solicitante')->label('Telefone'),
                TextEntry::make('titulo')->label('Título'),
                TextEntry::make('descricao')->label('Descrição')->columnSpanFull(),
                TextEntry::make('complexidade')
                    ->label('Complexidade')
                    ->badge()
                    ->formatStateUsing(fn (ComplexidadeChamadoEnum|string $state): string => ComplexidadeChamadoEnum::normalizar($state)->rotulo())
                    ->color(fn (ComplexidadeChamadoEnum|string $state): string => ComplexidadeChamadoEnum::normalizar($state)->cor()),
                TextEntry::make('setor.nome')->label('Setor'),
                TextEntry::make('tecnicoResponsavel.nome')
                    ->label('Técnico Responsável')
                    ->placeholder('Não atribuído'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->rotulo())
                    ->color(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->cor()),
                TextEntry::make('created_at')
                    ->label('Data de Abertura')
                    ->dateTime('d/m/Y H:i'),
                TextEntry::make('finalizado_em')
                    ->label('Data de Finalização')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('protocolo')
                    ->label('Protocolo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nome_solicitante')
                    ->label('Solicitante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_solicitante')
                    ->label('E-mail')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('setor.nome')
                    ->label('Setor')
                    ->sortable(),
                TextColumn::make('tecnicoResponsavel.nome')
                    ->label('Técnico')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('complexidade')
                    ->label('Complexidade')
                    ->badge()
                    ->formatStateUsing(fn (ComplexidadeChamadoEnum|string $state): string => ComplexidadeChamadoEnum::normalizar($state)->rotulo())
                    ->color(fn (ComplexidadeChamadoEnum|string $state): string => ComplexidadeChamadoEnum::normalizar($state)->cor()),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->rotulo())
                    ->color(fn (StatusChamadoEnum|string $state): string => StatusChamadoEnum::normalizar($state)->cor()),
                TextColumn::make('created_at')
                    ->label('Abertura')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusChamadoEnum::opcoes()),
                SelectFilter::make('setor_id')
                    ->label('Setor')
                    ->relationship('setor', 'nome'),
                SelectFilter::make('complexidade')
                    ->label('Complexidade')
                    ->options(ComplexidadeChamadoEnum::opcoes()),
                SelectFilter::make('tecnico_responsavel_id')
                    ->label('Técnico')
                    ->options(fn () => Usuario::query()->orderBy('nome')->pluck('nome', 'id')->all()),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar')
                    ->exporter(ChamadoExporter::class)
                    ->visible(fn (): bool => auth()->user()?->ehAdministrador() ?? false),
                Action::make('relatorioPdf')
                    ->label('Relatório PDF')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->color('gray')
                    ->url(fn (): string => route('filament.admin.chamados.relatorio-pdf')),
            ])
            ->recordActions([
                ViewAction::make()->label('Visualizar'),
                EditAction::make()
                    ->label('Editar')
                    ->visible(fn (Chamado $record): bool => auth()->user()->can('update', $record)),
                DeleteAction::make()
                    ->label('Excluir')
                    ->visible(fn (Chamado $record): bool => auth()->user()->can('delete', $record)),
                Action::make('assumir')
                    ->label('Assumir')
                    ->icon(Heroicon::OutlinedHandRaised)
                    ->color('primary')
                    ->visible(fn (Chamado $record): bool => auth()->user()->can('assumir', $record) && $record->tecnico_responsavel_id === null)
                    ->action(function (Chamado $record): void {
                        app(HistoricoChamadoService::class)->assumirChamado($record, auth()->user());
                    }),
                Action::make('adicionarHistorico')
                    ->label('Adicionar Histórico')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('info')
                    ->visible(fn (Chamado $record): bool => auth()->user()->can('adicionarHistorico', $record))
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(StatusChamadoEnum::opcoes())
                            ->required(),
                        Textarea::make('descricao')
                            ->label('Descrição')
                            ->required()
                            ->rows(4),
                        Toggle::make('visivel_solicitante')
                            ->label('Visível ao solicitante')
                            ->default(false),
                    ])
                    ->action(function (Chamado $record, array $data): void {
                        app(HistoricoChamadoService::class)->adicionar($record, auth()->user(), $data);
                    }),
                Action::make('finalizar')
                    ->label('Finalizar')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->modalHeading('Finalizar chamado')
                    ->modalDescription('Informe o motivo e o texto da finalização. O solicitante receberá um e-mail com link para avaliação.')
                    ->schema(FinalizarChamadoFormulario::campos())
                    ->visible(fn (Chamado $record): bool => auth()->user()->can('finalizar', $record) && $record->status !== StatusChamadoEnum::FINALIZADO)
                    ->action(function (Chamado $record, array $data): void {
                        app(ChamadoService::class)->finalizar($record, auth()->user(), $data);
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $usuario = auth()->user();

        if ($usuario && ! $usuario->ehAdministrador()) {
            $query->where('setor_id', $usuario->setor_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChamados::route('/'),
            'create' => CreateChamado::route('/create'),
            'view' => ViewChamado::route('/{record}'),
            'edit' => EditChamado::route('/{record}/edit'),
        ];
    }
}
