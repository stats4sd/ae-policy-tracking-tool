<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Resources\AePrincipleResource;
use App\Filament\Admin\Resources\CountryResource;
use App\Filament\Admin\Resources\PriorityActionResource;
use App\Filament\Admin\Resources\RecommendationResource;
use App\Filament\Admin\Resources\TypeResource;
use App\Filament\Admin\Resources\UserResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
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
                ->item(NavigationItem::make('Return to Tool')
                ->url('/')
                ->icon('heroicon-o-arrow-left'))
                    ->groups([
                        NavigationGroup::make('Lookup Lists')
                            ->items([
                                ...AePrincipleResource::getNavigationItems(),
                                ...RecommendationResource::getNavigationItems(),
                                ...PriorityActionResource::getNavigationItems(),
                                ...TypeResource::getNavigationItems(),
                                ...CountryResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make('User Management')
                            ->items([
                                ...UserResource::getNavigationItems(),
                            ]),
                    ]);
            });
    }
}
