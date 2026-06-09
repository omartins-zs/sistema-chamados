<?php

namespace Tests\Feature;

use Tests\TestCase;

class PainelControllerTest extends TestCase
{
    public function test_rota_painel_redireciona_para_admin(): void
    {
        $response = $this->get(route('painel'));

        $response->assertRedirect('/admin');
    }
}
