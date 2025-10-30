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

        Route::get('/policy-documents/{document}/content', [\App\Http\Controllers\PolicyDocumentController::class, 'getContent'])
            ->name('policy-document.content');


        Route::apiResource('highlights', \App\Http\Controllers\HighlightController::class)->only([
            'store',
        ]);

    });
