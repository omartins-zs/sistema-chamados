<?php

namespace App\Jobs;

use App\Mail\ChamadoCriadoMail;
use App\Models\Chamado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class EnviarEmailChamadoCriadoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Chamado $chamado,
    ) {}

    public function handle(): void
    {
        Mail::to($this->chamado->email_solicitante)
            ->send(new ChamadoCriadoMail($this->chamado));
    }
}
