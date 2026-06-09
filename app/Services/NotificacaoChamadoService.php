<?php

namespace App\Services;

use App\Enums\StatusChamadoEnum;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Setor;
use App\Models\Usuario;
use Filament\Notifications\Notification;

class NotificacaoChamadoService
{
    public function notificarChamadoCriado(Chamado $chamado): void
    {
        $this->enviarNotificacao(
            'Chamado criado',
            "Chamado criado com sucesso. Protocolo: {$chamado->protocolo}.",
            'success'
        );
    }

    public function notificarChamadoAssumido(Chamado $chamado, Usuario $tecnico): void
    {
        $this->enviarNotificacao(
            'Chamado assumido',
            "Chamado {$chamado->protocolo} assumido por {$tecnico->nome}.",
            'success'
        );
    }

    public function notificarHistoricoAdicionado(HistoricoChamado $historico): void
    {
        $historico->loadMissing('chamado');

        $this->enviarNotificacao(
            'Histórico adicionado',
            'Histórico adicionado com sucesso.',
            'info'
        );
    }

    public function notificarStatusAlterado(Chamado $chamado, StatusChamadoEnum $status): void
    {
        $this->enviarNotificacao(
            'Status alterado',
            "Status do chamado {$chamado->protocolo} alterado para {$status->rotulo()}.",
            'warning'
        );
    }

    public function notificarChamadoFinalizado(Chamado $chamado): void
    {
        $this->enviarNotificacao(
            'Chamado finalizado',
            "Chamado {$chamado->protocolo} finalizado com sucesso.",
            'success'
        );
    }

    public function notificarEmailAvaliacaoEnviado(Chamado $chamado): void
    {
        $this->enviarNotificacao(
            'E-mail enviado',
            "E-mail de avaliação enviado ao solicitante do chamado {$chamado->protocolo}.",
            'success'
        );
    }

    public function notificarTecnicoCriado(Usuario $usuario): void
    {
        $this->enviarNotificacao(
            'Técnico criado',
            "Técnico {$usuario->nome} criado com sucesso.",
            'success'
        );
    }

    public function notificarSetorCriado(Setor $setor): void
    {
        $this->enviarNotificacao(
            'Setor criado',
            "Setor {$setor->nome} criado com sucesso.",
            'success'
        );
    }

    public function notificarAvaliacaoRegistrada(AvaliacaoChamado $avaliacao): void
    {
        $avaliacao->loadMissing('chamado');

        $this->enviarNotificacao(
            'Avaliação registrada',
            "Avaliação registrada para o chamado {$avaliacao->chamado->protocolo}.",
            'success'
        );
    }

    private function enviarNotificacao(string $titulo, string $corpo, string $tipo): void
    {
        $notification = Notification::make()
            ->title($titulo)
            ->body($corpo);

        match ($tipo) {
            'success' => $notification->success(),
            'warning' => $notification->warning(),
            'danger' => $notification->danger(),
            default => $notification->info(),
        };

        $notification->send();
    }
}
