<?php

namespace Tests\Unit;

use App\Jobs\EnviarEmailChamadoCriadoJob;
use App\Jobs\EnviarEmailChamadoFinalizadoJob;
use App\Mail\ChamadoCriadoMail;
use App\Mail\ChamadoFinalizadoAvaliacaoMail;
use App\Models\Chamado;
use App\Services\NotificacaoChamadoService;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
    }

    public function test_job_email_chamado_criado(): void
    {
        Mail::fake();

        $chamado = Chamado::factory()->create();

        (new EnviarEmailChamadoCriadoJob($chamado))->handle();

        Mail::assertQueued(ChamadoCriadoMail::class, function (ChamadoCriadoMail $mail) use ($chamado): bool {
            return $mail->chamado->id === $chamado->id;
        });
    }

    public function test_job_email_chamado_finalizado(): void
    {
        Mail::fake();

        $chamado = Chamado::factory()->finalizado()->create();

        (new EnviarEmailChamadoFinalizadoJob($chamado))->handle(app(NotificacaoChamadoService::class));

        Mail::assertQueued(ChamadoFinalizadoAvaliacaoMail::class);
    }

    public function test_job_criado_envia_para_email_do_solicitante(): void
    {
        Mail::fake();

        $chamado = Chamado::factory()->create([
            'email_solicitante' => 'solicitante@example.com',
        ]);

        (new EnviarEmailChamadoCriadoJob($chamado))->handle();

        Mail::assertQueued(ChamadoCriadoMail::class, function (ChamadoCriadoMail $mail) use ($chamado): bool {
            return $mail->hasTo('solicitante@example.com')
                && $mail->chamado->id === $chamado->id;
        });
    }

    public function test_job_finalizado_envia_para_email_do_solicitante(): void
    {
        Mail::fake();

        $chamado = Chamado::factory()->finalizado()->create([
            'email_solicitante' => 'finalizado@example.com',
        ]);

        (new EnviarEmailChamadoFinalizadoJob($chamado))->handle(app(NotificacaoChamadoService::class));

        Mail::assertQueued(ChamadoFinalizadoAvaliacaoMail::class, function (ChamadoFinalizadoAvaliacaoMail $mail): bool {
            return $mail->hasTo('finalizado@example.com');
        });
    }
}
