<?php

namespace App\Mail;

use App\Models\Chamado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChamadoFinalizadoAvaliacaoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Chamado $chamado,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Avalie o atendimento do chamado {$this->chamado->protocolo}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chamados.finalizado',
            with: [
                'chamado' => $this->chamado,
                'urlAvaliacao' => route('chamados.avaliar', [
                    'protocolo' => $this->chamado->protocolo,
                    'token' => $this->chamado->token_avaliacao,
                ]),
            ],
        );
    }
}
