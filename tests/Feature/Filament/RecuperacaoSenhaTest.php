<?php

namespace Tests\Feature\Filament;

use App\Models\Usuario;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class RecuperacaoSenhaTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_de_solicitacao_de_reset_carrega(): void
    {
        $this->get(Filament::getPanel('admin')->getRequestPasswordResetUrl())
            ->assertSuccessful();
    }

    public function test_usuario_ativo_recebe_link_de_recuperacao_por_email(): void
    {
        Notification::fake();

        $usuario = Usuario::factory()->administrador()->create([
            'email' => 'reset@chamados.local',
        ]);

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => $usuario->email])
            ->call('request')
            ->assertNotified();

        Notification::assertSentTo($usuario, ResetPassword::class);
    }
}
