<?php

namespace App\Providers\Filament;

use App\Filament\App\Clusters\Setup\SetupCluster;
use App\Filament\App\Pages\AssessmentOverview;
use App\Filament\App\Pages\RegisterAssessment;
use App\Filament\App\Pages\Summary;
use App\Filament\App\Resources\Extracts\ExtractResource;
use App\Filament\App\Resources\PolicyDocuments\PolicyDocumentResource;
use App\Models\Assessment;
use App\Models\PriorityAction;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Stats4sd\FilamentTeamManagement\Filament\Auth\Login;
use Stats4sd\FilamentTeamManagement\Filament\Auth\Register;
use WatheqAlshowaiter\FilamentStickyTableHeader\StickyTableHeaderPlugin;

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
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset()
            ->globalSearch(false)
            ->colors([
                'primary' => '#119E83',
                'success' => '#17B978',
                'warning' => '#FFB822',
                'danger' => '#FF5B5B',
                'info' => '#3490DC',
                // 'gray' => '#6B7280',
            ])
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            // to include role register, program register, register filament pages from package stats4sd/filament-team-management
            ->discoverPages(in: app_path('../vendor/stats4sd/filament-team-management/src/Filament/App/Pages'), for: 'Stats4sd\\FilamentTeamManagement\\Filament\\App\\Pages')
            ->discoverClusters(in: app_path('Filament/App/Clusters'), for: 'App\\Filament\\App\\Clusters')
            ->pages([
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
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
                $priorityActionItems = PriorityAction::orderBy('id')
                    ->get()
                    ->map(fn (PriorityAction $pa) => NavigationItem::make($pa->code_and_short_name)
                        ->url(ExtractResource::getUrl('index').'?tab='.$pa->id)
                        ->isActiveWhen(fn () => request()->routeIs('filament.app.resources.extracts.index')
                            && request()->query('tab') === $pa->id)
                    )
                    ->all();

                return $builder
                    ->groups([
                        NavigationGroup::make()->items([
                            ...SetupCluster::getNavigationItems(),
                            ...PolicyDocumentResource::getNavigationItems(),
                        ]),
                        NavigationGroup::make('2. Review Extracts')
                            ->icon(Heroicon::OutlinedCheckBadge)
                            ->items($priorityActionItems),
                        NavigationGroup::make()->items([
                            ...AssessmentOverview::getNavigationItems(),
                            ...Summary::getNavigationItems(),
                        ]),
                    ]);
            })
            ->userMenuItems([
                Action::make('Admin Panel')
                    ->url('/admin')
                    ->icon('heroicon-o-shield-check'),
            ])
            ->topNavigation(true)
            ->databaseNotifications()
            ->viteTheme('resources/css/filament/app/theme.css')
            ->plugins([
                StickyTableHeaderPlugin::make(),
            ]);
    }
}
