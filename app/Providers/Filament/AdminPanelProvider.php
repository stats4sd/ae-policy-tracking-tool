<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use App\Filament\Admin\Resources\AePrincipleResource;
use App\Filament\Admin\Resources\AssessmentResource;
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
            ->colors([
                'primary' => "#119E83",
                'success' => "#17B978",
                'warning' => "#FFB822",
                'danger' => "#FF5B5B",
                'info' => "#3490DC",
                'gray' => '#6B7280',
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                AccountWidget::class,
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
                                ...AssessmentResource::getNavigationItems(),
                                ...UserResource::getNavigationItems(),
                                ...RoleResource::getNavigationItems(),
                                ...PermissionResource::getNavigationItems(),
                            ]),
                    ]);
            })
            ->plugins([
                FilamentSpatieRolesPermissionsPlugin::make(),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
