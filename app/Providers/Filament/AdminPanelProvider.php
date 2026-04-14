<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use App\Filament\Admin\Pages\AdminDashboard;
use App\Filament\Admin\Resources\AePrinciples\AePrincipleResource;
use App\Filament\Admin\Resources\Assessments\AssessmentResource;
use App\Filament\Admin\Resources\Countries\CountryResource;
use App\Filament\Admin\Resources\Languages\LanguageResource;
use App\Filament\Admin\Resources\PriorityActions\PriorityActionResource;
use App\Filament\Admin\Resources\Recommendations\RecommendationResource;
use App\Filament\Admin\Resources\Scores\ScoreResource;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Stats4sd\FilamentTeamManagement\Http\Middleware\AuthenticateThroughDefaultPanel;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('/admin')
            ->colors([
                'primary' => '#119E83',
                'success' => '#17B978',
                'warning' => '#FFB822',
                'danger' => '#FF5B5B',
                'info' => '#3490DC',
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
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
                AuthenticateThroughDefaultPanel::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        NavigationItem::make('Return to Tool')
                            ->url('/')
                            ->icon('heroicon-o-arrow-left'),
                        ...AdminDashboard::getNavigationItems(),
                    ])
                    ->groups([
                        NavigationGroup::make('Lookup Lists')
                            ->items([
                                ...AePrincipleResource::getNavigationItems(),
                                ...RecommendationResource::getNavigationItems(),
                                ...PriorityActionResource::getNavigationItems(),
                                ...ScoreResource::getNavigationItems(),
                                ...CountryResource::getNavigationItems(),
                                ...LanguageResource::getNavigationItems(),
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
            ->databaseNotifications()
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
