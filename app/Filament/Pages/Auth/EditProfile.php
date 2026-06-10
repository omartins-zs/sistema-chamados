<?php

namespace App\Filament\Pages\Auth;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use SensitiveParameter;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    protected static ?string $title = 'Meu perfil';

    public static function getLabel(): string
    {
        return 'Meu perfil';
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('nome')
            ->label('Nome')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('senha')
            ->label('Nova senha')
            ->validationAttribute('nova senha')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->rule(Password::default())
            ->showAllValidationMessages()
            ->autocomplete('new-password')
            ->dehydrated(fn (#[SensitiveParameter] $state): bool => filled($state))
            ->dehydrateStateUsing(fn (#[SensitiveParameter] $state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('senhaConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('senhaConfirmation')
            ->label('Confirmar nova senha')
            ->validationAttribute('confirmação da nova senha')
            ->password()
            ->autocomplete('new-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (Get $get): bool => filled($get('senha')))
            ->dehydrated(false);
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('currentPassword')
            ->label('Senha atual')
            ->validationAttribute('senha atual')
            ->belowContent('Informe a senha atual para alterar o e-mail ou definir uma nova senha.')
            ->password()
            ->autocomplete('current-password')
            ->currentPassword(guard: Filament::getAuthGuard())
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (Get $get): bool => filled($get('senha')) || ($get('email') !== $this->getUser()->getAttributeValue('email')))
            ->dehydrated(false);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }

    public function save(): void
    {
        $senhaAlterada = filled($this->data['senha'] ?? null);

        parent::save();

        if (! $senhaAlterada || ! request()->hasSession()) {
            return;
        }

        $usuario = $this->getUser()->refresh();

        request()->session()->put([
            'password_hash_'.Filament::getAuthGuard() => $usuario->getAttributeValue('senha'),
        ]);

        $this->data['senha'] = null;
        $this->data['senhaConfirmation'] = null;
    }
}
