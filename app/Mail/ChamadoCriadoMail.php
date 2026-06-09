<?php

namespace App\Mail;

use App\Models\Chamado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChamadoCriadoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Chamado $chamado,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Chamado {$this->chamado->protocolo} registrado com sucesso",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chamados.criado',
            with: [
                'chamado' => $this->chamado,
                'urlSucesso' => route('chamados.sucesso', $this->chamado->protocolo),
            ],
        );
    }
}
