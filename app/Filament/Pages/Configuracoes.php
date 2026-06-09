<?php

namespace App\Filament\Pages;

use App\Enums\TipoUsuarioEnum;
use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use App\Models\Setor;
use App\Models\Usuario;
use App\Services\QueueStatusService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Configuracoes extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configurações';

    protected static ?string $title = 'Configurações';

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->ehAdministrador() ?? false;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.pages.configuracoes'),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getResumo(): array
    {
        return [
            'chamados' => Chamado::query()->count(),
            'tecnicos' => Usuario::query()->where('tipo_usuario', TipoUsuarioEnum::TECNICO)->count(),
            'setores' => Setor::query()->count(),
            'avaliacoes' => AvaliacaoChamado::query()->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getInformacoesSistema(): array
    {
        $ambiente = config('app.env');
        $mailer = config('mail.default');
        $fila = config('queue.default');

        return [
            'nome' => config('app.name'),
            'ambiente_rotulo' => $this->rotuloAmbiente($ambiente),
            'ambiente_cor' => $this->corAmbiente($ambiente),
            'laravel' => app()->version(),
            'php' => PHP_VERSION,
            'url' => config('app.url'),
            'locale' => strtoupper((string) config('app.locale')),
            'timezone' => config('app.timezone'),
            'fila_rotulo' => $this->rotuloFila($fila),
            'mailer_rotulo' => $this->rotuloMailer($mailer),
            'mailer_cor' => $mailer === 'log' ? 'warning' : 'success',
            'mailer_alerta' => $mailer === 'log',
            'debug' => (bool) config('app.debug'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatusFila(): array
    {
        return app(QueueStatusService::class)->obterStatus();
    }

    /**
     * @return array<int, Setor>
     */
    public function getSetoresListagem(): array
    {
        return Setor::query()
            ->withCount(['usuarios', 'chamados'])
            ->orderBy('nome')
            ->get()
            ->all();
    }

    private function rotuloAmbiente(string $ambiente): string
    {
        return match ($ambiente) {
            'local' => 'Desenvolvimento Local',
            'staging', 'homolog' => 'Homologação',
            'production' => 'Produção',
            'testing' => 'Testes',
            default => ucfirst($ambiente),
        };
    }

    private function corAmbiente(string $ambiente): string
    {
        return match ($ambiente) {
            'local' => 'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-950/50 dark:text-amber-200 dark:ring-amber-800',
            'staging', 'homolog' => 'bg-sky-100 text-sky-800 ring-sky-200 dark:bg-sky-950/50 dark:text-sky-200 dark:ring-sky-800',
            'production' => 'bg-emerald-100 text-emerald-800 ring-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-200 dark:ring-emerald-800',
            default => 'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700',
        };
    }

    private function rotuloFila(string $driver): string
    {
        return match ($driver) {
            'database' => 'Banco de dados',
            'redis' => 'Redis',
            'sync' => 'Síncrono (imediato)',
            'beanstalkd' => 'Beanstalkd',
            'sqs' => 'Amazon SQS',
            default => ucfirst($driver),
        };
    }

    private function rotuloMailer(string $mailer): string
    {
        return match ($mailer) {
            'log' => 'Log (modo teste)',
            'smtp' => 'SMTP',
            'sendmail' => 'Sendmail',
            'mailgun' => 'Mailgun',
            'ses' => 'Amazon SES',
            'postmark' => 'Postmark',
            default => ucfirst($mailer),
        };
    }
}
