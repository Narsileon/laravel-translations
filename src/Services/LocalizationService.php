<?php

namespace Narsil\Localization\Services;

#region USE

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class LocalizationService
{
    #region PUBLIC METHODS

    /**
     * @return array
     */
    public static function getTranslations(): array
    {
        $locale = App::getLocale();

        return Cache::rememberForever("narsil:translations:$locale", function ()
        {
            $language = Language::locale();

            $dictionary = Translation::dictionary($language->{Language::ID})->get();

            $translations = [];

            foreach ($dictionary as $translation)
            {
                $translationValue = $translation->{Translation::RELATIONSHIP_VALUES}?->first(function ($value) use ($locale)
                {
                    return $value->{TranslationValue::RELATIONSHIP_LANGUAGE}->{Language::LOCALE} === $locale;
                });

                $translations[$translation->{Translation::KEY}] = [
                    Translation::ID => $translation->{Translation::ID},
                    TranslationValue::VALUE => $translationValue?->{TranslationValue::VALUE} ?? $translation->{Translation::DEFAULT_VALUE},
                ];
            }

            return $translations;
        });
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function trans(string $key): string
    {
        $translations = LocalizationService::getTranslations();

        $value = $translations[$key][TranslationValue::VALUE] ?? $key;

        return $value;
    }

    #endregion
}
