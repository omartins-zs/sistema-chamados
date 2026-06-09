<?php

namespace App\Filament\Resources\Usuarios;

use App\Enums\TipoUsuarioEnum;
use App\Filament\Resources\Usuarios\Pages\ManageUsuarios;
use App\Models\Usuario;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Técnicos';

    protected static ?string $modelLabel = 'Técnico';

    protected static ?string $pluralModelLabel = 'Técnicos';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'nome';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('senha')
                    ->label('Senha')
                    ->password()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Select::make('setor_id')
                    ->label('Setor')
                    ->relationship('setor', 'nome')
                    ->searchable()
                    ->preload(),
                Select::make('tipo_usuario')
                    ->label('Tipo de Usuário')
                    ->options(TipoUsuarioEnum::opcoes())
                    ->required(),
                Toggle::make('ativo')
                    ->label('Ativo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')->label('Nome')->searchable()->sortable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('setor.nome')->label('Setor')->sortable(),
                TextColumn::make('tipo_usuario')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (TipoUsuarioEnum $state): string => $state->rotulo()),
                IconColumn::make('ativo')->label('Ativo')->boolean(),
            ])
            ->filters([
                SelectFilter::make('setor_id')
                    ->label('Setor')
                    ->relationship('setor', 'nome'),
                SelectFilter::make('tipo_usuario')
                    ->label('Tipo')
                    ->options(TipoUsuarioEnum::opcoes()),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Excluir'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsuarios::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->ehAdministrador() ?? false;
    }
}
