<?php

namespace Tests\Unit;

use App\Services\QueueStatusService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class QueueStatusServiceTest extends TestCase
{
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
}
