<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_pagina_inicial_exibe_landing_institucional(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Sistema de Chamados Técnicos', false);
        $response->assertSee('Abrir Chamado', false);
        $response->assertSee('Consultar Chamado', false);
    }
}
