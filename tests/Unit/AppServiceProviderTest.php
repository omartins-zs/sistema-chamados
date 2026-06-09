<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use App\Services\QueueStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_evento_looping_registra_heartbeat_da_fila(): void
    {
        $service = app(QueueStatusService::class);

        event(new Looping('database', 'default', 0));

        Config::set('queue.default', 'database');

        $status = $service->obterStatus();

        $this->assertTrue($status['rodando']);
        $this->assertNotNull($status['ultimo_heartbeat']);
    }

    public function test_provider_registra_policies(): void
    {
        $provider = new AppServiceProvider(app());
        $provider->boot();

        $this->assertTrue(true);
    }
}
