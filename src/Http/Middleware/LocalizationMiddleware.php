<?php

namespace Narsil\Localization\Http\Middleware;

#region USE

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Narsil\Localization\Constants\LocalizationConfig;
use Narsil\Localization\Constants\LocalizationSettings;
use Narsil\Localization\Enums\LocaleEnum;
use Narsil\Localization\Models\Language;
use Narsil\Settings\Models\Setting;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class LocalizationMiddleware
{
    #region PUBLIC METHODS

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $this->handleFallbackLocale();
        $this->handleLocale($request);

        return $next($request);
    }

    #endregion

    #region PRIVATE METHODS

    /**
     * @return void
     */
    private function handleFallbackLocale(): void
    {
        if ($fallbackLocale = Setting::get(LocalizationSettings::FALLBACK_LOCALE))
        {
            App::setFallbackLocale($fallbackLocale);
        }
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    private function handleLocale(Request $request): void
    {
        $locales = Config::get(LocalizationConfig::LOCALES, array_map(fn($case) => $case->value, LocaleEnum::cases()));

        $locale = Session::get(Language::LOCALE, $request->getPreferredLanguage($locales));

        App::setLocale($locale);
    }

    #endregion
}
