<?php

namespace Narsil\Localization\Http\Controllers;

#region USE

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Narsil\Localization\Http\Requests\LocaleRequest;
use Narsil\Localization\Models\Language;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class LocaleController extends Controller
{
    #region PUBLIC METHODS

    /**
     * @return RedirectResponse
     */
    public function __invoke(LocaleRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        $language = Language::firstWhere([
            Language::LOCALE => $attributes[Language::LOCALE],
        ]);

        if ($language)
        {
            Session::put(Language::LOCALE, $attributes[Language::LOCALE]);
        }

        return back();
    }

    #endregion
}
