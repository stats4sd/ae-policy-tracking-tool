<?php

namespace App\Providers;

use App\Filament\App\Pages\RegisterAssessment;
use Filament\Pages\Auth\Login;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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
        \Illuminate\Database\Eloquent\Model::unguard();

        // footer on some pages
        FilamentView::registerRenderHook(PanelsRenderHook::BODY_END, fn() => view('filament.app.pages.register-assessment-footer'),
            scopes: [
                RegisterAssessment::class,
                Login::class,
            ]);

        FilamentView::registerRenderHook(PanelsRenderHook::BODY_START, fn() => view('filament.app.pages.register-assessment-header'),
            scopes: [
                RegisterAssessment::class,
                Login::class,
            ]);

    }
}
