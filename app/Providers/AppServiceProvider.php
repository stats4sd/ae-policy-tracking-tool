<?php

namespace App\Providers;

use App\Filament\App\Pages\RegisterAssessment;
use App\Filament\App\Resources\PolicyDocumentResource\Pages\ReviewPolicyDocument;
use Filament\Pages\Auth\Login;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // unguarded all models across the app
        \Eloquent::unguard();

        // footer on some pages
        FilamentView::registerRenderHook(PanelsRenderHook::BODY_END, fn () => view('filament.app.render-hook-components.register-assessment-footer'),
            scopes: [
                RegisterAssessment::class,
                Login::class,
            ]);

        FilamentView::registerRenderHook(PanelsRenderHook::BODY_START, fn () => view('filament.app.render-hook-components.register-assessment-header'),
            scopes: [
                RegisterAssessment::class,
                Login::class,
            ]);

        // Render hooks for the entire App Panel:
        FilamentView::registerRenderHook(PanelsRenderHook::TOPBAR_START, fn () => view('filament.app.render-hook-components.topbar'));

        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_AFTER,
            fn (): string => Blade::render('@vite("resources/js/assessment-app.js")'),
            //            fn (): string => Blade::render('<script> console.log("Review Policy Document Page Loaded"); </script>'),
            scopes: [ReviewPolicyDocument::class],
        );

    }
}
