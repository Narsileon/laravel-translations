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

            $dictionaryEntries = Translation::dictionary($language->{Language::ID})->get();

            $translations = [];

            foreach ($dictionaryEntries as $directionaryEntry)
            {
                $translations[$directionaryEntry->{Translation::KEY}] = [
                    Translation::ID => $directionaryEntry->{Translation::ID},
                    Translation::RELATIONSHIP_VALUE => $directionaryEntry->{Translation::RELATIONSHIP_VALUE}?->{TranslationValue::VALUE},
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

        $value = $translations[$key][Translation::RELATIONSHIP_VALUE] ?? $key;

        return $value;
    }

    #endregion
}
