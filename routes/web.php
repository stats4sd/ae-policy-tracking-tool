<?php

use App\Http\Controllers\ExtractController;
use App\Http\Controllers\PolicyDocumentController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\TypeController;
use App\Models\Assessment;
use App\Models\Recommendation;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/app');
});

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ], function () {

        Route::get('/{assessment}/print-review', function (Assessment $assessment) {
            $recommendations = Recommendation::with(['aePrinciples', 'priorityActions'])->get();

            return view('filament.app.pages.review', compact('assessment', 'recommendations'));
        })->name('assessment.print-review');

        Route::get('/policy-documents/{document}/pages', [PolicyDocumentController::class, 'getPages'])
            ->name('policy-document.pages');

        Route::get('/policy-documents/{document}/extracts', [PolicyDocumentController::class, 'getExtracts']);

        Route::apiResource('extracts', ExtractController::class)->only([
            'store', 'update', 'destroy',
        ]);

        Route::apiResource('recommendations', RecommendationController::class)->only([
            'index', 'show',
        ]);

        Route::apiResource('types', TypeController::class)->only([
            'index',
        ]);

    });
