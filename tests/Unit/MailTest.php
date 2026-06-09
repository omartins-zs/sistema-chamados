<?php

namespace Tests\Unit;

use App\Mail\ChamadoCriadoMail;
use App\Mail\ChamadoFinalizadoAvaliacaoMail;
use App\Models\Chamado;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_chamado_criado_mail_envelope_e_conteudo(): void
    {
        $chamado = Chamado::factory()->create();
        $mail = new ChamadoCriadoMail($chamado);

        $this->assertStringContainsString($chamado->protocolo, $mail->envelope()->subject);
        $this->assertSame('emails.chamados.criado', $mail->content()->markdown);
        $this->assertSame($chamado->id, $mail->content()->with['chamado']->id);
        $this->assertSame(
            route('chamados.sucesso', $chamado->protocolo),
            $mail->content()->with['urlSucesso']
        );
    }

    public function test_chamado_finalizado_avaliacao_mail_envelope_e_conteudo(): void
    {
        $chamado = Chamado::factory()->finalizado()->create([
            'token_avaliacao' => 'token-de-teste',
        ]);
        $mail = new ChamadoFinalizadoAvaliacaoMail($chamado);

        $this->assertStringContainsString($chamado->protocolo, $mail->envelope()->subject);
        $this->assertSame('emails.chamados.finalizado', $mail->content()->markdown);
        $this->assertSame(
            route('chamados.avaliar', [
                'protocolo' => $chamado->protocolo,
                'token' => $chamado->token_avaliacao,
            ]),
            $mail->content()->with['urlAvaliacao']
        );
    }
}
