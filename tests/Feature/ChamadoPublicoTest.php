<?php

namespace Tests\Feature;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Jobs\EnviarEmailChamadoCriadoJob;
use App\Models\Chamado;
use App\Models\Setor;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ChamadoPublicoTest extends TestCase
{
    use RefreshDatabase;

    private Setor $setor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->setor = Setor::query()->firstOrFail();
    }

    public function test_usuario_consegue_criar_chamado_publico(): void
    {
        Queue::fake();

        $response = $this->post(route('chamados.salvar'), $this->dadosValidos());

        $response->assertRedirect();
        $this->assertDatabaseCount('chamados', 1);
    }

    public function test_protocolo_e_gerado_corretamente(): void
    {
        Queue::fake();

        $this->post(route('chamados.salvar'), $this->dadosValidos());

        $chamado = Chamado::query()->first();

        $this->assertMatchesRegularExpression('/^CHM-\d{4}-\d{6}$/', $chamado->protocolo);
    }

    public function test_chamado_nasce_como_em_aberto(): void
    {
        Queue::fake();

        $this->post(route('chamados.salvar'), $this->dadosValidos());

        $this->assertSame(StatusChamadoEnum::EM_ABERTO, Chamado::query()->first()->status);
    }

    public function test_chamado_e_vinculado_ao_setor_correto(): void
    {
        Queue::fake();

        $this->post(route('chamados.salvar'), $this->dadosValidos());

        $this->assertSame($this->setor->id, Chamado::query()->first()->setor_id);
    }

    public function test_dados_invalidos_retornam_erro(): void
    {
        $response = $this->from(route('chamados.criar'))
            ->post(route('chamados.salvar'), []);

        $response->assertRedirect(route('chamados.criar'));
        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('chamados', 0);
    }

    public function test_email_de_confirmacao_e_disparado(): void
    {
        Queue::fake();

        $this->post(route('chamados.salvar'), $this->dadosValidos());

        Queue::assertPushed(EnviarEmailChamadoCriadoJob::class);
    }

    public function test_tela_de_sucesso_exibe_dados_do_chamado(): void
    {
        Queue::fake();

        $this->post(route('chamados.salvar'), $this->dadosValidos());
        $chamado = Chamado::query()->firstOrFail();

        $response = $this->get(route('chamados.sucesso', $chamado->protocolo));

        $response->assertOk();
        $response->assertSee($chamado->protocolo);
        $response->assertSee($chamado->nome_solicitante);
    }

    /**
     * @return array<string, mixed>
     */
    private function dadosValidos(): array
    {
        return [
            'nome_solicitante' => 'Fabiana Costa',
            'email_solicitante' => 'fabiana@example.com',
            'telefone_solicitante' => '(11) 99999-9999',
            'titulo' => 'Novo site institucional',
            'descricao' => 'Precisamos de um novo site com a paleta da empresa.',
            'complexidade' => ComplexidadeChamadoEnum::MEDIA->value,
            'setor_id' => $this->setor->id,
        ];
    }
}
