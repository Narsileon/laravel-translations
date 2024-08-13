<?php

namespace Narsil\Localization\Services;

#region USE

use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\FileLoader;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class LocalizationLoaderService extends FileLoader
{
    #region PUBLIC METHODS

    /**
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        $defaultTranslations = parent::load($locale, $group, $namespace);

        $translations =  Cache::rememberForever(
            "translations_{$locale}_{$group}",
            function () use ($locale, $group)
            {
                return TranslationValue::query()
                    ->with([
                        TranslationValue::RELATIONSHIP_KEY,
                        TranslationValue::RELATIONSHIP_LANGUAGE,
                    ])
                    ->whereRelation(TranslationValue::RELATIONSHIP_LANGUAGE, Language::LOCALE, '=', $locale)
                    ->whereRelation(TranslationValue::RELATIONSHIP_KEY, Translation::KEY, 'like', "{$group}.%")
                    ->pluck(TranslationValue::VALUE, TranslationValue::RELATIONSHIP_KEY . '.' .  Translation::KEY)
                    ->toArray();
            }
        );

        return array_merge($defaultTranslations, $translations);
    }

    #endregion
}
