<?php

namespace App\Providers;

use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Setor;
use App\Models\Usuario;
use App\Policies\AvaliacaoChamadoPolicy;
use App\Policies\ChamadoPolicy;
use App\Policies\HistoricoChamadoPolicy;
use App\Policies\SetorPolicy;
use App\Policies\UsuarioPolicy;
use App\Services\QueueStatusService;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Authenticatable::class, Usuario::class);
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        Event::listen(Looping::class, function (): void {
            app(QueueStatusService::class)->registrarHeartbeat();
        });

        Gate::policy(Chamado::class, ChamadoPolicy::class);
        Gate::policy(Setor::class, SetorPolicy::class);
        Gate::policy(Usuario::class, UsuarioPolicy::class);
        Gate::policy(AvaliacaoChamado::class, AvaliacaoChamadoPolicy::class);
        Gate::policy(HistoricoChamado::class, HistoricoChamadoPolicy::class);
    }
}
