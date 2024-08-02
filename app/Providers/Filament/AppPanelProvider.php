<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\AssessmentOverview;
use App\Filament\App\Pages\OngoingMonitoring;
use App\Filament\App\Pages\RegisterAssessment;
use App\Filament\App\Pages\Review;
use App\Filament\App\Pages\StakeholderEngagement;
use App\Filament\App\Resources\PolicyResource;
use App\Models\Assessment;
use App\Models\Policy;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
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
            ->id('app')
            ->path('')
            ->tenant(Assessment::class)
            ->tenantRegistration(RegisterAssessment::class)
            ->login()
            ->colors([
                'primary' => "#119E83",
                'success' => "#17B978",
                'warning' => "#FFB822",
                'danger' => "#FF5B5B",
                'info' => "#3490DC",
                'grey' => '#6B7280',
            ])
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
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
                        ...StakeholderEngagement::getNavigationItems(),
                        ...OngoingMonitoring::getNavigationItems(),
                    ]);
            })
            ->topNavigation(true)
            ->viteTheme('resources/css/filament/app/theme.css');
    }
}
