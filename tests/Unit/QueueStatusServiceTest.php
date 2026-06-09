<?php

namespace Tests\Unit;

use App\Services\QueueStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class QueueStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fila_sync_nao_precisa_worker(): void
    {
        Config::set('queue.default', 'sync');

        $status = app(QueueStatusService::class)->obterStatus();

        $this->assertTrue($status['rodando']);
        $this->assertFalse($status['precisa_worker']);
        $this->assertSame('Processamento imediato', $status['rotulo']);
    }

    public function test_heartbeat_registrado(): void
    {
        $service = app(QueueStatusService::class);
        $service->registrarHeartbeat();

        Config::set('queue.default', 'database');

        $status = $service->obterStatus();

        $this->assertTrue($status['rodando']);
        $this->assertNotNull($status['ultimo_heartbeat']);
    }

    public function test_fila_database_sem_worker_fica_parada(): void
    {
        Config::set('queue.default', 'database');

        $status = app(QueueStatusService::class)->obterStatus();

        $this->assertFalse($status['rodando']);
        $this->assertTrue($status['precisa_worker']);
        $this->assertSame('Worker parado', $status['rotulo']);
    }

    public function test_conta_jobs_pendentes_e_falhos(): void
    {
        Config::set('queue.default', 'database');

        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'available_at' => time(),
            'created_at' => time(),
        ]);

        DB::table('failed_jobs')->insert([
            'uuid' => (string) Str::uuid(),
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{}',
            'exception' => 'Erro simulado',
            'failed_at' => now(),
        ]);

        $status = app(QueueStatusService::class)->obterStatus();

        $this->assertSame(1, $status['jobs_pendentes']);
        $this->assertSame(1, $status['jobs_falhos']);
    }

    public function test_heartbeat_expirado_indica_worker_inativo(): void
    {
        Config::set('queue.default', 'database');

        Cache::put(
            'queue.worker.heartbeat',
            now()->subMinutes(5)->toIso8601String(),
            now()->addMinutes(3),
        );

        $status = app(QueueStatusService::class)->obterStatus();

        $this->assertFalse($status['rodando']);
    }

    public function test_contagem_zero_quando_tabelas_de_fila_nao_existem(): void
    {
        Config::set('queue.default', 'database');

        Schema::drop('jobs');
        Schema::drop('failed_jobs');

        $status = app(QueueStatusService::class)->obterStatus();

        $this->assertSame(0, $status['jobs_pendentes']);
        $this->assertSame(0, $status['jobs_falhos']);
    }
}
