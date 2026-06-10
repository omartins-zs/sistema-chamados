<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Widgets\ChamadosEmAtendimentoWidget;
use App\Filament\Widgets\ChamadosEncerradosWidget;
use App\Filament\Widgets\ChamadosEvolucaoWidget;
use App\Filament\Widgets\ResumoGeralChamadosWidget;
use App\Http\Controllers\Admin\ChamadoRelatorioController;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->profile(EditProfile::class, isSimple: false)
            ->passwordReset()
            ->databaseNotifications()
            ->darkMode()
            ->brandName('Sistema de Chamados')
            ->maxContentWidth(Width::Full)
            ->sidebarFullyCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::hex('#415A77'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                ResumoGeralChamadosWidget::class,
                ChamadosEvolucaoWidget::class,
                ChamadosEmAtendimentoWidget::class,
                ChamadosEncerradosWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->authPasswordBroker('usuarios')
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): View => view('filament.hooks.novo-chamado-topbar'),
            )
            ->routes(function (): void {
                Route::get('/chamados/relatorio-pdf', [ChamadoRelatorioController::class, 'lista'])
                    ->name('chamados.relatorio-pdf');
                Route::get('/chamados/{chamado}/relatorio-pdf', [ChamadoRelatorioController::class, 'individual'])
                    ->name('chamados.relatorio-pdf-individual');
            });
    }
}
