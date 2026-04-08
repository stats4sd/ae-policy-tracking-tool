<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/app');
});

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ], function () {

        Route::get('/{assessment}/print-review', function () {
            return view('filament.app.pages.review');
        })->name('assessment.print-review');

        Route::get('/policy-documents/{document}/pages', [\App\Http\Controllers\PolicyDocumentController::class, 'getPages'])
            ->name('policy-document.pages');

        Route::get('/policy-documents/{document}/extracts', [\App\Http\Controllers\PolicyDocumentController::class, 'getExtracts']);

        Route::apiResource('extracts', \App\Http\Controllers\ExtractController::class)->only([
            'store', 'update', 'destroy',
        ]);

        Route::apiResource('recommendations', \App\Http\Controllers\RecommendationController::class)->only([
            'index', 'show',
        ]);

        Route::apiResource('types', \App\Http\Controllers\TypeController::class)->only([
            'index',
        ]);

    });
