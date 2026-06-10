<?php

namespace Database\Seeders;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Enums\TipoUsuarioEnum;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ChamadoSeeder extends Seeder
{
    private int $sequencia = 1;

    public function run(): void
    {
        AvaliacaoChamado::query()->delete();
        HistoricoChamado::query()->delete();
        Chamado::query()->delete();

        $ano = now()->year;
        $setores = Setor::query()->orderBy('nome')->get()->keyBy('nome');
        $tecnicosPorSetor = $this->carregarTecnicosPorSetor($setores);

        $this->criarChamadosFixosDemonstracao($ano, $setores, $tecnicosPorSetor);

        if (filter_var(env('E2E_SEED_MINIMAL', false), FILTER_VALIDATE_BOOL)) {
            $this->destacarChamadosDemonstracaoNaListagem($ano);

            return;
        }

        $distribuicaoStatus = [
            [StatusChamadoEnum::EM_ABERTO, 7],
            [StatusChamadoEnum::ACESSADO, 6],
            [StatusChamadoEnum::EM_ANDAMENTO, 7],
            [StatusChamadoEnum::AGUARDANDO_CLIENTE, 6],
            [StatusChamadoEnum::AGUARDANDO_TERCEIROS, 6],
            [StatusChamadoEnum::PAUSADO, 6],
            [StatusChamadoEnum::BLOQUEADO, 6],
            [StatusChamadoEnum::CONCLUIDO, 6],
            [StatusChamadoEnum::FINALIZADO, 12],
            [StatusChamadoEnum::CANCELADO, 6],
        ];

        $indiceSetor = 0;
        $nomesSetores = $setores->keys()->values();
        $indiceTecnicoGlobal = 0;

        foreach ($distribuicaoStatus as [$status, $quantidade]) {
            for ($i = 0; $i < $quantidade; $i++) {
                $nomeSetor = $nomesSetores[$indiceSetor % $nomesSetores->count()];
                $indiceSetor++;

                $setor = $setores[$nomeSetor];
                $tecnicosSetor = $tecnicosPorSetor[$setor->id];
                $tecnicoPrincipal = $tecnicosSetor[$indiceTecnicoGlobal % $tecnicosSetor->count()];
                $indiceTecnicoGlobal++;

                $comDoisTecnicos = $status !== StatusChamadoEnum::EM_ABERTO
                    && $status !== StatusChamadoEnum::ACESSADO
                    && $i % 3 === 0
                    && $tecnicosSetor->count() > 1;

                $tecnicoSecundario = $comDoisTecnicos
                    ? $tecnicosSetor->first(fn (Usuario $t): bool => $t->id !== $tecnicoPrincipal->id)
                    : null;

                $diasAtras = 45 - ($this->sequencia % 40);
                $inicio = now()->subDays($diasAtras)->setTime(8 + ($this->sequencia % 9), ($this->sequencia * 7) % 60);

                $this->criarChamadoGerado(
                    $ano,
                    $setor,
                    $status,
                    $tecnicoPrincipal,
                    $tecnicoSecundario,
                    $inicio,
                    $this->sequencia,
                );
            }
        }

        $this->destacarChamadosDemonstracaoNaListagem($ano);
    }

    private function destacarChamadosDemonstracaoNaListagem(int $ano): void
    {
        $protocolos = [
            sprintf('CHM-%d-000001', $ano),
            sprintf('CHM-%d-000002', $ano),
            sprintf('CHM-%d-000003', $ano),
        ];

        $momento = now();

        foreach ($protocolos as $indice => $protocolo) {
            $chamado = Chamado::query()->where('protocolo', $protocolo)->first();

            if ($chamado === null) {
                continue;
            }

            $registro = $momento->copy()->subMinutes(count($protocolos) - $indice);
            $chamado->created_at = $registro;
            $chamado->updated_at = $registro;
            $chamado->saveQuietly();
        }
    }

    /**
     * @param  Collection<string, Setor>  $setores
     * @param  Collection<int, Collection<int, Usuario>>  $tecnicosPorSetor
     */
    private function criarChamadosFixosDemonstracao(
        int $ano,
        Collection $setores,
        Collection $tecnicosPorSetor,
    ): void {
        $setorDesenvolvimento = $setores['Desenvolvimento'];
        $setorSuporte = $setores['Suporte Técnico/Infra'];
        $lucas = $tecnicosPorSetor[$setorDesenvolvimento->id]->firstWhere('email', 'lucas-martins@chamados.local');
        $marcos = $tecnicosPorSetor[$setorSuporte->id]->firstWhere('email', 'marcos-silva@chamados.local');

        $chamadoAberto = $this->criarChamado(
            $ano,
            [
                'nome_solicitante' => 'Fabiana Costa',
                'email_solicitante' => 'fabiana@example.com',
                'telefone_solicitante' => '(11) 98888-7777',
                'titulo' => 'Novo site institucional',
                'descricao' => 'Precisamos de um novo site com a paleta da empresa e área de chamados.',
                'complexidade' => ComplexidadeChamadoEnum::MEDIA,
                'setor_id' => $setorDesenvolvimento->id,
                'tecnico_responsavel_id' => null,
                'status' => StatusChamadoEnum::EM_ABERTO,
            ],
            now()->subDays(3)->setTime(9, 15),
        );

        $inicioAndamento = now()->subDays(12)->setTime(10, 30);
        $chamadoAndamento = $this->criarChamado(
            $ano,
            [
                'nome_solicitante' => 'Ricardo Mendes',
                'email_solicitante' => 'ricardo@example.com',
                'telefone_solicitante' => '(11) 97777-6666',
                'titulo' => 'Servidor de arquivos indisponível',
                'descricao' => 'O servidor de arquivos da contabilidade não está respondendo desde ontem.',
                'complexidade' => ComplexidadeChamadoEnum::ALTA,
                'setor_id' => $setorSuporte->id,
                'tecnico_responsavel_id' => $marcos->id,
                'status' => StatusChamadoEnum::EM_ANDAMENTO,
            ],
            $inicioAndamento,
        );

        $this->registrarHistorico(
            $chamadoAndamento,
            $marcos,
            StatusChamadoEnum::ACESSADO,
            'Verificando conectividade e logs do servidor de arquivos.',
            true,
            $inicioAndamento->copy()->addHours(2),
        );
        $this->registrarHistorico(
            $chamadoAndamento,
            $marcos,
            StatusChamadoEnum::EM_ANDAMENTO,
            'Reiniciando serviço e monitorando estabilidade.',
            true,
            $inicioAndamento->copy()->addHours(5),
        );

        $inicioDev = now()->subDays(8)->setTime(14, 0);
        $chamadoDev = $this->criarChamado(
            $ano,
            [
                'nome_solicitante' => 'Ana Paula',
                'email_solicitante' => 'ana@example.com',
                'telefone_solicitante' => '(11) 96666-5555',
                'titulo' => 'Módulo de relatórios',
                'descricao' => 'Implementar exportação de relatórios em PDF no sistema interno.',
                'complexidade' => ComplexidadeChamadoEnum::MEDIA,
                'setor_id' => $setorDesenvolvimento->id,
                'tecnico_responsavel_id' => $lucas->id,
                'status' => StatusChamadoEnum::AGUARDANDO_CLIENTE,
            ],
            $inicioDev,
        );

        $this->registrarHistorico(
            $chamadoDev,
            $lucas,
            StatusChamadoEnum::ACESSADO,
            'Analisando requisitos do módulo de relatórios.',
            true,
            $inicioDev->copy()->addHours(3),
        );
        $this->registrarHistorico(
            $chamadoDev,
            $lucas,
            StatusChamadoEnum::EM_ANDAMENTO,
            'Estrutura do módulo criada. Iniciando protótipo da exportação.',
            true,
            $inicioDev->copy()->addDay(),
        );
        $this->registrarHistorico(
            $chamadoDev,
            $lucas,
            StatusChamadoEnum::AGUARDANDO_CLIENTE,
            'Protótipo pronto. Aguardando validação do layout pelo solicitante.',
            true,
            $inicioDev->copy()->addDays(2),
        );

        $chamadoAberto->update(['tecnico_responsavel_id' => null]);
    }

    private function criarChamadoGerado(
        int $ano,
        Setor $setor,
        StatusChamadoEnum $statusFinal,
        Usuario $tecnicoPrincipal,
        ?Usuario $tecnicoSecundario,
        Carbon $inicio,
        int $indice,
    ): void {
        $solicitante = $this->solicitanteParaIndice($indice);
        $titulo = $this->tituloParaSetor($setor->nome, $statusFinal, $indice);

        $dadosChamado = [
            'nome_solicitante' => $solicitante['nome'],
            'email_solicitante' => $solicitante['email'],
            'telefone_solicitante' => $solicitante['telefone'],
            'titulo' => $titulo,
            'descricao' => "Chamado de demonstração #{$indice} para o setor {$setor->nome}. Status alvo: {$statusFinal->rotulo()}.",
            'complexidade' => $this->complexidadeParaIndice($indice),
            'setor_id' => $setor->id,
            'tecnico_responsavel_id' => $statusFinal === StatusChamadoEnum::EM_ABERTO ? null : $tecnicoPrincipal->id,
            'status' => $statusFinal,
        ];

        if ($statusFinal === StatusChamadoEnum::FINALIZADO) {
            $finalizadoEm = $inicio->copy()->addDays(3 + ($indice % 5))->setTime(16, 30);
            $dadosChamado['finalizado_em'] = $finalizadoEm;
            $dadosChamado['token_avaliacao'] = Str::random(64);
            $dadosChamado['expira_token_avaliacao_em'] = $finalizadoEm->copy()->addDays(30);
        }

        $chamado = $this->criarChamado($ano, $dadosChamado, $inicio);

        if ($statusFinal === StatusChamadoEnum::EM_ABERTO) {
            return;
        }

        $cadeia = $this->cadeiaStatus($statusFinal);
        $momento = $inicio->copy()->addHours(1);
        $tecnicoAtual = $tecnicoPrincipal;
        $pontoTroca = (int) floor(count($cadeia) / 2);

        foreach ($cadeia as $posicao => $statusHistorico) {
            if ($tecnicoSecundario !== null && $posicao >= $pontoTroca) {
                if ($tecnicoAtual->id === $tecnicoPrincipal->id) {
                    $tecnicoAtual = $tecnicoSecundario;
                    $chamado->update(['tecnico_responsavel_id' => $tecnicoSecundario->id]);
                }
            }

            $descricao = $this->descricaoHistorico($statusHistorico, $tecnicoAtual, $tecnicoSecundario !== null && $tecnicoAtual->id === $tecnicoSecundario->id);

            $this->registrarHistorico(
                $chamado,
                $tecnicoAtual,
                $statusHistorico,
                $descricao,
                $this->visivelParaSolicitante($statusHistorico),
                $momento,
            );

            $momento = $momento->copy()->addHours(4 + ($posicao % 3) * 2);
        }

        if ($statusFinal === StatusChamadoEnum::FINALIZADO) {
            $tecnicoFinal = $chamado->tecnico_responsavel_id
                ? Usuario::query()->find($chamado->tecnico_responsavel_id)
                : $tecnicoAtual;

            $this->registrarHistorico(
                $chamado,
                $tecnicoFinal,
                StatusChamadoEnum::FINALIZADO,
                'Motivo da finalização: Problema resolvido\n\nServiço restabelecido e validado com o solicitante.',
                true,
                $chamado->finalizado_em ?? $momento,
            );

            if ($indice % 3 !== 0) {
                AvaliacaoChamado::query()->create([
                    'chamado_id' => $chamado->id,
                    'nota_satisfacao' => 2 + ($indice % 4),
                    'nota_tempo_resolucao' => 3 + ($indice % 3),
                    'comentario' => $this->comentarioAvaliacao($indice),
                ]);
            }
        }

        $chamado->update(['status' => $statusFinal]);
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    private function criarChamado(int $ano, array $dados, Carbon $criadoEm): Chamado
    {
        $protocolo = sprintf('CHM-%d-%06d', $ano, $this->sequencia++);

        $chamado = Chamado::query()->create(array_merge($dados, [
            'protocolo' => $protocolo,
        ]));

        $chamado->created_at = $criadoEm;
        $chamado->updated_at = $criadoEm;
        $chamado->saveQuietly();

        return $chamado;
    }

    private function registrarHistorico(
        Chamado $chamado,
        Usuario $tecnico,
        StatusChamadoEnum $status,
        string $descricao,
        bool $visivelSolicitante,
        Carbon $momento,
    ): HistoricoChamado {
        $historico = HistoricoChamado::query()->create([
            'chamado_id' => $chamado->id,
            'tecnico_id' => $tecnico->id,
            'status' => $status,
            'descricao' => $descricao,
            'visivel_solicitante' => $visivelSolicitante,
            'created_at' => $momento,
            'updated_at' => $momento,
        ]);

        $chamado->update(['status' => $status]);

        return $historico;
    }

    /**
     * @param  Collection<string, Setor>  $setores
     * @return Collection<int, Collection<int, Usuario>>
     */
    private function carregarTecnicosPorSetor(Collection $setores): Collection
    {
        $tecnicos = Usuario::query()
            ->where('tipo_usuario', TipoUsuarioEnum::TECNICO)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return $setores->mapWithKeys(
            fn (Setor $setor): array => [
                $setor->id => $tecnicos->where('setor_id', $setor->id)->values(),
            ]
        );
    }

    /**
     * @return list<StatusChamadoEnum>
     */
    private function cadeiaStatus(StatusChamadoEnum $statusFinal): array
    {
        return match ($statusFinal) {
            StatusChamadoEnum::ACESSADO => [
                StatusChamadoEnum::ACESSADO,
            ],
            StatusChamadoEnum::EM_ANDAMENTO => [
                StatusChamadoEnum::ACESSADO,
                StatusChamadoEnum::EM_ANDAMENTO,
            ],
            StatusChamadoEnum::AGUARDANDO_CLIENTE => [
                StatusChamadoEnum::ACESSADO,
                StatusChamadoEnum::EM_ANDAMENTO,
                StatusChamadoEnum::AGUARDANDO_CLIENTE,
            ],
            StatusChamadoEnum::AGUARDANDO_TERCEIROS => [
                StatusChamadoEnum::ACESSADO,
                StatusChamadoEnum::EM_ANDAMENTO,
                StatusChamadoEnum::AGUARDANDO_TERCEIROS,
            ],
            StatusChamadoEnum::PAUSADO => [
                StatusChamadoEnum::ACESSADO,
                StatusChamadoEnum::EM_ANDAMENTO,
                StatusChamadoEnum::PAUSADO,
            ],
            StatusChamadoEnum::BLOQUEADO => [
                StatusChamadoEnum::ACESSADO,
                StatusChamadoEnum::EM_ANDAMENTO,
                StatusChamadoEnum::BLOQUEADO,
            ],
            StatusChamadoEnum::CONCLUIDO => [
                StatusChamadoEnum::ACESSADO,
                StatusChamadoEnum::EM_ANDAMENTO,
                StatusChamadoEnum::CONCLUIDO,
            ],
            StatusChamadoEnum::FINALIZADO => [
                StatusChamadoEnum::ACESSADO,
                StatusChamadoEnum::EM_ANDAMENTO,
                StatusChamadoEnum::CONCLUIDO,
            ],
            StatusChamadoEnum::CANCELADO => [
                StatusChamadoEnum::ACESSADO,
                StatusChamadoEnum::EM_ANDAMENTO,
                StatusChamadoEnum::CANCELADO,
            ],
            default => [],
        };
    }

    private function descricaoHistorico(
        StatusChamadoEnum $status,
        Usuario $tecnico,
        bool $trocaTecnico,
    ): string {
        $prefixoTroca = $trocaTecnico ? 'Assumindo chamado do colega de setor. ' : '';

        return $prefixoTroca.match ($status) {
            StatusChamadoEnum::ACESSADO => "{$tecnico->nome} acessou o chamado e iniciou a análise.",
            StatusChamadoEnum::EM_ANDAMENTO => 'Diagnóstico em andamento. Ações corretivas sendo aplicadas.',
            StatusChamadoEnum::AGUARDANDO_CLIENTE => 'Aguardando retorno do solicitante com informações complementares.',
            StatusChamadoEnum::AGUARDANDO_TERCEIROS => 'Aguardando retorno de fornecedor externo para continuidade.',
            StatusChamadoEnum::PAUSADO => 'Atendimento pausado conforme alinhamento com o solicitante.',
            StatusChamadoEnum::BLOQUEADO => 'Chamado bloqueado por dependência técnica ou pendência crítica.',
            StatusChamadoEnum::CONCLUIDO => 'Solução implementada. Aguardando confirmação final.',
            StatusChamadoEnum::CANCELADO => 'Chamado cancelado a pedido do solicitante ou por duplicidade.',
            default => 'Atualização registrada no chamado.',
        };
    }

    private function visivelParaSolicitante(StatusChamadoEnum $status): bool
    {
        return true;
    }

    private function complexidadeParaIndice(int $indice): ComplexidadeChamadoEnum
    {
        return match ($indice % 4) {
            0 => ComplexidadeChamadoEnum::BAIXA,
            1 => ComplexidadeChamadoEnum::MEDIA,
            2 => ComplexidadeChamadoEnum::ALTA,
            default => ComplexidadeChamadoEnum::CRITICA,
        };
    }

    /**
     * @return array{nome: string, email: string, telefone: string}
     */
    private function solicitanteParaIndice(int $indice): array
    {
        $nomes = [
            'Carla Nunes', 'Diego Fonseca', 'Eliane Prado', 'Felipe Rocha',
            'Gabriela Lima', 'Henrique Dias', 'Isabela Moura', 'João Victor',
            'Karina Alves', 'Leonardo Pires', 'Mariana Teixeira', 'Nicolas Barros',
            'Olívia Campos', 'Paulo Henrique', 'Renata Freitas', 'Samuel Costa',
        ];

        $nome = $nomes[$indice % count($nomes)];
        $slug = Str::slug($nome);

        return [
            'nome' => $nome,
            'email' => "{$slug}.{$indice}@example.com",
            'telefone' => sprintf('(11) 9%04d-%04d', 1000 + ($indice % 8999), 1000 + (($indice * 13) % 8999)),
        ];
    }

    private function tituloParaSetor(string $setor, StatusChamadoEnum $status, int $indice): string
    {
        $titulos = match ($setor) {
            'Gerência de TI' => [
                'Revisão de política de backup',
                'Auditoria de acessos VPN',
                'Planejamento de capacidade do datacenter',
                'Atualização do inventário de ativos',
            ],
            'Desenvolvimento' => [
                'Correção de bug no portal interno',
                'API de integração com ERP',
                'Melhoria de performance em relatórios',
                'Nova tela de cadastro de usuários',
            ],
            'Telefonia/CFTV' => [
                'Câmera offline no estacionamento',
                'Ramal sem tom no setor financeiro',
                'Gravação CFTV com falhas',
                'Configuração de URA telefônica',
            ],
            default => [
                'Estação sem acesso à rede',
                'Impressora corporativa indisponível',
                'Lentidão no servidor de arquivos',
                'Falha em autenticação de e-mail',
            ],
        };

        $base = $titulos[$indice % count($titulos)];

        return "{$base} ({$status->rotulo()})";
    }

    private function comentarioAvaliacao(int $indice): string
    {
        $comentarios = [
            'Atendimento cordial e solução dentro do esperado.',
            'Resolveram rápido, mas faltou comunicação intermediária.',
            'Excelente suporte, problema resolvido na primeira intervenção.',
            'Demorou um pouco, porém a solução foi definitiva.',
            'Satisfeito com o profissionalismo da equipe.',
        ];

        return $comentarios[$indice % count($comentarios)];
    }
}
