<?php

#region USE

use Illuminate\Support\Facades\Route;
use Narsil\Localization\Http\Controllers\LocaleController;

#endregion

Route::middleware([
    'web'
])->group(function ()
{
    Route::patch('locale', LocaleController::class)->name('locale');
});
