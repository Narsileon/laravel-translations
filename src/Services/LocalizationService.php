<?php

namespace Narsil\NarsilFramework\Services;

#region USE

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
        $languageId = Language::locale()->{Language::ID};

        return Cache::rememberForever("narsil:translations:$languageId", function () use ($languageId)
        {
            $dictionaryEntries = Translation::dictionary($languageId)->get();

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
