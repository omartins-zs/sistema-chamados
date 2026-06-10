<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\Auth\EditProfile;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\Concerns\AutenticaFilament;
use Tests\TestCase;

class PerfilUsuarioTest extends TestCase
{
    use AutenticaFilament;

    public function test_pagina_de_perfil_carrega_para_usuario_autenticado(): void
    {
        $this->autenticarAdministrador();

        $this->get(Filament::getPanel('admin')->getProfileUrl())
            ->assertSuccessful();
    }

    public function test_usuario_atualiza_nome_e_senha_no_perfil(): void
    {
        $usuario = $this->autenticarAdministrador();

        Livewire::test(EditProfile::class)
            ->fillForm([
                'nome' => 'Administrador Atualizado',
                'email' => $usuario->email,
                'senha' => 'nova-senha-segura',
                'senhaConfirmation' => 'nova-senha-segura',
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertNotified();

        $usuario->refresh();

        $this->assertSame('Administrador Atualizado', $usuario->nome);
        $this->assertTrue(Hash::check('nova-senha-segura', $usuario->senha));
    }
}
