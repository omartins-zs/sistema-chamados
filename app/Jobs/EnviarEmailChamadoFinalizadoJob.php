<?php

namespace App\Jobs;

use App\Mail\ChamadoFinalizadoAvaliacaoMail;
use App\Models\Chamado;
use App\Services\NotificacaoChamadoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class EnviarEmailChamadoFinalizadoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Chamado $chamado,
    ) {}

    public function handle(NotificacaoChamadoService $notificacaoChamadoService): void
    {
        Mail::to($this->chamado->email_solicitante)
            ->send(new ChamadoFinalizadoAvaliacaoMail($this->chamado));

        $notificacaoChamadoService->notificarEmailAvaliacaoEnviado($this->chamado);
    }
}
