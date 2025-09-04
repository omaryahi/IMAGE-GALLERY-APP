<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::post('/language/{lang}', [LanguageController::class, 'switch'])
    ->name('language.switch');
