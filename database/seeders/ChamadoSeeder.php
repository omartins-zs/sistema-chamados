<?php

namespace Database\Seeders;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class ChamadoSeeder extends Seeder
{
    public function run(): void
    {
        $setorDesenvolvimento = Setor::query()->where('nome', 'Desenvolvimento')->firstOrFail();
        $setorSuporte = Setor::query()->where('nome', 'Suporte Técnico/Infra')->firstOrFail();

        $lucas = Usuario::query()->where('email', 'lucas-martins@chamados.local')->firstOrFail();
        $marcos = Usuario::query()->where('email', 'marcos-silva@chamados.local')->firstOrFail();

        $chamadoAberto = Chamado::query()->updateOrCreate(
            ['protocolo' => 'CHM-'.now()->year.'-000001'],
            [
                'nome_solicitante' => 'Fabiana Costa',
                'email_solicitante' => 'fabiana@example.com',
                'telefone_solicitante' => '(11) 98888-7777',
                'titulo' => 'Novo site institucional',
                'descricao' => 'Precisamos de um novo site com a paleta da empresa e área de chamados.',
                'complexidade' => ComplexidadeChamadoEnum::MEDIA,
                'setor_id' => $setorDesenvolvimento->id,
                'status' => StatusChamadoEnum::EM_ABERTO,
            ]
        );

        $chamadoAndamento = Chamado::query()->updateOrCreate(
            ['protocolo' => 'CHM-'.now()->year.'-000002'],
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
            ]
        );

        HistoricoChamado::query()->updateOrCreate(
            [
                'chamado_id' => $chamadoAndamento->id,
                'tecnico_id' => $marcos->id,
                'descricao' => 'Verificando conectividade e logs do servidor de arquivos.',
            ],
            [
                'status' => StatusChamadoEnum::ACESSADO,
                'visivel_solicitante' => true,
            ]
        );

        HistoricoChamado::query()->updateOrCreate(
            [
                'chamado_id' => $chamadoAndamento->id,
                'tecnico_id' => $marcos->id,
                'descricao' => 'Reiniciando serviço e monitorando estabilidade.',
            ],
            [
                'status' => StatusChamadoEnum::EM_ANDAMENTO,
                'visivel_solicitante' => true,
            ]
        );

        $chamadoDev = Chamado::query()->updateOrCreate(
            ['protocolo' => 'CHM-'.now()->year.'-000003'],
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
            ]
        );

        HistoricoChamado::query()->updateOrCreate(
            [
                'chamado_id' => $chamadoDev->id,
                'tecnico_id' => $lucas->id,
                'descricao' => 'Estrutura do módulo criada. Aguardando validação do layout pelo solicitante.',
            ],
            [
                'status' => StatusChamadoEnum::AGUARDANDO_CLIENTE,
                'visivel_solicitante' => true,
            ]
        );

        // Garante que o chamado aberto não tenha técnico
        $chamadoAberto->update(['tecnico_responsavel_id' => null]);
    }
}
