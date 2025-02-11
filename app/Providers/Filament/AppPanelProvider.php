<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\AssessmentOverview;
use App\Filament\App\Pages\RegisterAssessment;
use App\Filament\App\Pages\Review;
use App\Filament\App\Resources\PolicyResource;
use App\Filament\App\Resources\UserResource;
use App\Models\Assessment;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->tenant(Assessment::class)
            ->tenantRegistration(RegisterAssessment::class)
            ->login()
            ->passwordReset()
            ->colors([
                'primary' => '#119E83',
                'success' => '#17B978',
                'warning' => '#FFB822',
                'danger' => '#FF5B5B',
                'info' => '#3490DC',
                'gray' => '#6B7280',
            ])
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            // to include role register, program register, register filament pages from package stats4sd/filament-team-management
            ->discoverPages(in: app_path('../vendor/stats4sd/filament-team-management/src/Filament/App/Pages'), for: 'Stats4sd\\FilamentTeamManagement\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        ...AssessmentOverview::getNavigationItems(),
                        ...PolicyResource::getNavigationItems(),
                        ...Review::getNavigationItems(),
                        NavigationItem::make('Admin Panel')
                            ->icon('heroicon-o-shield-check')
                            ->url('/admin')
                            ->visible(function () {
                                return auth()->user()->isAdmin();
                            }),
                        NavigationItem::make('Feedback Form')
                        ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                        ->url('https://odk.stats4sd.org/-/single/tnBvd5N3wzFvqZigrV1gZ7CaLof0agi?st=laZ3QZZ5icr1DoKtm7KrKM0qUZCr52K81$1oXhEm5NjjKiaMVCrNeun9F2WBR1Kd'),
                    ]);
            })
            ->topNavigation(true)
            ->viteTheme('resources/css/filament/app/theme.css');
    }
}
