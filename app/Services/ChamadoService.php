<?php

namespace App\Services;

use App\Enums\StatusChamadoEnum;
use App\Http\Requests\FinalizarChamadoRequest;
use App\Jobs\EnviarEmailChamadoCriadoJob;
use App\Jobs\EnviarEmailChamadoFinalizadoJob;
use App\Models\Chamado;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ChamadoService
{
    public function __construct(
        private readonly NotificacaoChamadoService $notificacaoChamadoService,
        private readonly HistoricoChamadoService $historicoChamadoService,
    ) {}

    /**
     * @param  array<string, mixed>  $dados
     */
    public function criar(array $dados): Chamado
    {
        return DB::transaction(function () use ($dados): Chamado {
            $chamado = Chamado::query()->create([
                'protocolo' => $this->gerarProtocolo(),
                'nome_solicitante' => $dados['nome_solicitante'],
                'email_solicitante' => $dados['email_solicitante'],
                'telefone_solicitante' => $dados['telefone_solicitante'],
                'titulo' => $dados['titulo'],
                'descricao' => $dados['descricao'],
                'complexidade' => $dados['complexidade'],
                'setor_id' => $dados['setor_id'],
                'status' => StatusChamadoEnum::EM_ABERTO,
            ]);

            EnviarEmailChamadoCriadoJob::dispatch($chamado);

            return $chamado->load('setor');
        });
    }

    public function gerarProtocolo(): string
    {
        $ano = now()->year;
        $prefixo = "CHM-{$ano}-";

        $ultimoNumero = Chamado::query()
            ->where('protocolo', 'like', "{$prefixo}%")
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('protocolo');

        $sequencia = 1;

        if ($ultimoNumero !== null) {
            $sequencia = (int) substr($ultimoNumero, -6) + 1;
        }

        return $prefixo.str_pad((string) $sequencia, 6, '0', STR_PAD_LEFT);
    }

    public function alterarStatus(Chamado $chamado, StatusChamadoEnum $status): Chamado
    {
        $chamado->update(['status' => $status]);

        return $chamado->refresh();
    }

    /**
     * @param  array{motivo: string, descricao: string}  $dados
     */
    public function finalizar(Chamado $chamado, Usuario $tecnico, array $dados): Chamado
    {
        $validador = Validator::make($dados, (new FinalizarChamadoRequest)->rules(), (new FinalizarChamadoRequest)->messages());

        if ($validador->fails()) {
            throw new ValidationException($validador);
        }

        $motivo = trim($dados['motivo']);
        $descricao = trim($dados['descricao']);

        return DB::transaction(function () use ($chamado, $tecnico, $motivo, $descricao): Chamado {
            if ($chamado->status === StatusChamadoEnum::FINALIZADO) {
                throw ValidationException::withMessages([
                    'status' => 'Este chamado já está finalizado.',
                ]);
            }

            $token = $this->gerarTokenAvaliacao();

            $chamado->update([
                'status' => StatusChamadoEnum::FINALIZADO,
                'finalizado_em' => now(),
                'token_avaliacao' => $token,
                'expira_token_avaliacao_em' => now()->addDays(30),
            ]);

            $this->historicoChamadoService->registrarFinalizacao(
                $chamado->refresh(),
                $tecnico,
                $motivo,
                $descricao,
            );

            EnviarEmailChamadoFinalizadoJob::dispatch($chamado->refresh());

            $this->notificacaoChamadoService->notificarChamadoFinalizado($chamado);

            return $chamado;
        });
    }

    public function gerarTokenAvaliacao(): string
    {
        do {
            $token = Str::random(64);
        } while (Chamado::query()->where('token_avaliacao', $token)->exists());

        return $token;
    }

    public function buscarPorProtocolo(string $protocolo): ?Chamado
    {
        return Chamado::query()
            ->with(['setor', 'tecnicoResponsavel', 'historicosPublicos.tecnico.setor', 'avaliacao'])
            ->where('protocolo', $protocolo)
            ->first();
    }
}
