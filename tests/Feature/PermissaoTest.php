<?php

namespace Tests\Feature;

use App\Models\Chamado;
use App\Models\Setor;
use App\Models\Usuario;
use App\Policies\ChamadoPolicy;
use App\Policies\SetorPolicy;
use App\Policies\UsuarioPolicy;
use Database\Seeders\SetorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissaoTest extends TestCase
{
    use RefreshDatabase;

    private ChamadoPolicy $chamadoPolicy;

    private SetorPolicy $setorPolicy;

    private UsuarioPolicy $usuarioPolicy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SetorSeeder::class);
        $this->chamadoPolicy = new ChamadoPolicy;
        $this->setorPolicy = new SetorPolicy;
        $this->usuarioPolicy = new UsuarioPolicy;
    }

    public function test_admin_ve_todos_os_chamados(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $setores = Setor::query()->take(2)->get();

        $chamado1 = Chamado::factory()->create(['setor_id' => $setores[0]->id]);
        $chamado2 = Chamado::factory()->create(['setor_id' => $setores[1]->id]);

        $this->assertTrue($this->chamadoPolicy->view($admin, $chamado1));
        $this->assertTrue($this->chamadoPolicy->view($admin, $chamado2));
    }

    public function test_tecnico_ve_apenas_chamados_do_seu_setor(): void
    {
        $setores = Setor::query()->take(2)->get();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setores[0]->id]);

        $chamadoMesmoSetor = Chamado::factory()->create(['setor_id' => $setores[0]->id]);
        $chamadoOutroSetor = Chamado::factory()->create(['setor_id' => $setores[1]->id]);

        $this->assertTrue($this->chamadoPolicy->view($tecnico, $chamadoMesmoSetor));
        $this->assertFalse($this->chamadoPolicy->view($tecnico, $chamadoOutroSetor));
    }

    public function test_tecnico_nao_gerencia_setores(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();
        $setor = Setor::query()->firstOrFail();

        $this->assertFalse($this->setorPolicy->viewAny($tecnico));
        $this->assertFalse($this->setorPolicy->create($tecnico));
        $this->assertFalse($this->setorPolicy->update($tecnico, $setor));
    }

    public function test_tecnico_nao_gerencia_outros_usuarios(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();
        $outro = Usuario::factory()->tecnico()->create();

        $this->assertFalse($this->usuarioPolicy->viewAny($tecnico));
        $this->assertFalse($this->usuarioPolicy->create($tecnico));
        $this->assertFalse($this->usuarioPolicy->update($tecnico, $outro));
    }

    public function test_tecnico_pode_finalizar_chamado_do_setor(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $this->assertTrue($this->chamadoPolicy->finalizar($tecnico, $chamado));
    }

    public function test_tecnico_nao_pode_excluir_chamado(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $this->assertFalse($this->chamadoPolicy->delete($tecnico, $chamado));
    }

    public function test_view_any_chamado_sempre_permitido(): void
    {
        $tecnico = Usuario::factory()->tecnico()->create();

        $this->assertTrue($this->chamadoPolicy->viewAny($tecnico));
    }

    public function test_apenas_admin_cria_chamado(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $tecnico = Usuario::factory()->tecnico()->create();

        $this->assertTrue($this->chamadoPolicy->create($admin));
        $this->assertFalse($this->chamadoPolicy->create($tecnico));
    }

    public function test_tecnico_atualiza_e_assume_chamado_do_setor(): void
    {
        $setor = Setor::query()->firstOrFail();
        $tecnico = Usuario::factory()->tecnico()->create(['setor_id' => $setor->id]);
        $chamado = Chamado::factory()->create(['setor_id' => $setor->id]);

        $this->assertTrue($this->chamadoPolicy->update($tecnico, $chamado));
        $this->assertTrue($this->chamadoPolicy->assumir($tecnico, $chamado));
        $this->assertTrue($this->chamadoPolicy->adicionarHistorico($tecnico, $chamado));
    }

    public function test_admin_gerencia_setores(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $setor = Setor::query()->firstOrFail();

        $this->assertTrue($this->setorPolicy->viewAny($admin));
        $this->assertTrue($this->setorPolicy->view($admin, $setor));
        $this->assertTrue($this->setorPolicy->create($admin));
        $this->assertTrue($this->setorPolicy->update($admin, $setor));
        $this->assertTrue($this->setorPolicy->delete($admin, $setor));
    }

    public function test_admin_gerencia_usuarios_mas_nao_exclui_a_si_mesmo(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $outro = Usuario::factory()->administrador()->create();

        $this->assertTrue($this->usuarioPolicy->viewAny($admin));
        $this->assertTrue($this->usuarioPolicy->view($admin, $outro));
        $this->assertTrue($this->usuarioPolicy->create($admin));
        $this->assertTrue($this->usuarioPolicy->update($admin, $outro));
        $this->assertTrue($this->usuarioPolicy->delete($admin, $outro));
        $this->assertFalse($this->usuarioPolicy->delete($admin, $admin));
    }
}
