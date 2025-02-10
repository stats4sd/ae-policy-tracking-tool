<?php

use Illuminate\Support\Facades\Route;

Route::get('/{assessment}/print-review', function () {
    return view('filament.app.pages.review');
})->name('assessment.print-review');


Route::get('/', function() {
    return redirect('/app');
});
