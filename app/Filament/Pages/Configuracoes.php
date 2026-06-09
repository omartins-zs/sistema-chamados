<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Setores\SetorResource;
use App\Filament\Widgets\ConfiguracoesStatsWidget;
use App\Models\Setor;
use App\Services\QueueStatusService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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

    public function getHeaderWidgets(): array
    {
        return [
            ConfiguracoesStatsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int
    {
        return 4;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Informações do Sistema')
                            ->description('Ambiente, versões e parâmetros ativos')
                            ->icon(Heroicon::OutlinedCpuChip)
                            ->schema([
                                View::make('filament.pages.partials.configuracoes-sistema'),
                            ]),
                        Section::make('E-mails e Filas')
                            ->description('Status do worker e envio de notificações')
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->schema([
                                View::make('filament.pages.partials.configuracoes-email-fila'),
                            ]),
                    ]),
                Section::make('Acesso Rápido')
                    ->description('Atalhos para áreas públicas e gestão interna')
                    ->icon(Heroicon::OutlinedLink)
                    ->schema([
                        View::make('filament.pages.partials.configuracoes-links'),
                    ]),
                Section::make('Setores Cadastrados')
                    ->description('Áreas de atendimento com técnicos e chamados vinculados')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->headerActions([
                        Action::make('gerenciarSetores')
                            ->label('Gerenciar setores')
                            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                            ->url(SetorResource::getUrl('index')),
                    ])
                    ->schema([
                        View::make('filament.pages.partials.configuracoes-setores'),
                    ]),
            ]);
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
            'ambiente_cor' => $this->corAmbienteFilament($ambiente),
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

    private function corAmbienteFilament(string $ambiente): string
    {
        return match ($ambiente) {
            'local' => 'warning',
            'staging', 'homolog' => 'info',
            'production' => 'success',
            default => 'gray',
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
