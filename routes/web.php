<?php

#region USE

use Illuminate\Support\Facades\Route;
use Narsil\Localization\Http\Controllers\LocaleController;
use Narsil\Localization\Http\Controllers\TranslationFetchController;

#endregion

Route::middleware([
    'web'
])->group(function ()
{
    Route::patch('locale', LocaleController::class)
        ->name('locale');

    Route::get('translations/fetch', TranslationFetchController::class)
        ->name('translations.fetch');
});
