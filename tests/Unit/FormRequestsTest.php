<?php

namespace Tests\Unit;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Http\Requests\AdicionarHistoricoChamadoRequest;
use App\Http\Requests\AtualizarStatusChamadoRequest;
use App\Http\Requests\ConsultarChamadoRequest;
use App\Http\Requests\CriarAvaliacaoRequest;
use App\Http\Requests\CriarChamadoRequest;
use App\Http\Requests\FinalizarChamadoRequest;
use App\Models\Setor;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FormRequestsTest extends TestCase
{
    use RefreshDatabase;

    private Setor $setor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->setor = Setor::query()->firstOrFail();
    }

    public function test_criar_chamado_request_rejeita_dados_invalidos(): void
    {
        $request = new CriarChamadoRequest;
        $validator = Validator::make([], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email_solicitante'));
    }

    public function test_criar_chamado_request_aceita_dados_validos(): void
    {
        $request = new CriarChamadoRequest;
        $validator = Validator::make([
            'nome_solicitante' => 'Maria Silva',
            'email_solicitante' => 'maria@example.com',
            'telefone_solicitante' => '(11) 98888-7777',
            'titulo' => 'Suporte ao sistema',
            'descricao' => 'Descrição com mais de dez caracteres.',
            'complexidade' => ComplexidadeChamadoEnum::MEDIA->value,
            'setor_id' => $this->setor->id,
        ], $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_consultar_chamado_request_rejeita_protocolo_invalido(): void
    {
        $request = new ConsultarChamadoRequest;
        $validator = Validator::make(['protocolo' => 'INVALIDO'], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
    }

    public function test_criar_avaliacao_request_rejeita_notas_fora_do_intervalo(): void
    {
        $request = new CriarAvaliacaoRequest;
        $validator = Validator::make([
            'nota_satisfacao' => 0,
            'nota_tempo_resolucao' => 6,
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
    }

    public function test_criar_avaliacao_request_aceita_dados_validos(): void
    {
        $request = new CriarAvaliacaoRequest;
        $validator = Validator::make([
            'nota_satisfacao' => 5,
            'nota_tempo_resolucao' => 4,
            'comentario' => 'Ótimo atendimento.',
        ], $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_finalizar_chamado_request_exige_motivo_e_descricao(): void
    {
        $request = new FinalizarChamadoRequest;
        $validator = Validator::make([
            'motivo' => 'ab',
            'descricao' => 'curta',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
    }

    public function test_adicionar_historico_request_rejeita_status_invalido(): void
    {
        $request = new AdicionarHistoricoChamadoRequest;
        $validator = Validator::make([
            'status' => 'status_inexistente',
            'descricao' => 'Texto válido.',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
    }

    public function test_adicionar_historico_request_aceita_dados_validos(): void
    {
        $request = new AdicionarHistoricoChamadoRequest;
        $validator = Validator::make([
            'status' => StatusChamadoEnum::EM_ANDAMENTO->value,
            'descricao' => 'Atualização registrada.',
            'visivel_solicitante' => true,
        ], $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_atualizar_status_request_exige_status(): void
    {
        $request = new AtualizarStatusChamadoRequest;
        $validator = Validator::make([], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
    }

    public function test_requests_autorizam_acesso(): void
    {
        $this->assertTrue((new CriarChamadoRequest)->authorize());
        $this->assertTrue((new ConsultarChamadoRequest)->authorize());
        $this->assertTrue((new CriarAvaliacaoRequest)->authorize());
        $this->assertTrue((new FinalizarChamadoRequest)->authorize());
        $this->assertTrue((new AdicionarHistoricoChamadoRequest)->authorize());
        $this->assertTrue((new AtualizarStatusChamadoRequest)->authorize());
    }
}
