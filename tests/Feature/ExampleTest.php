<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_pagina_inicial_redireciona_para_abrir_chamado(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/chamados/novo');
    }
}
