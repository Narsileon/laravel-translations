<?php

namespace Narsil\Localization\Observers;

#region USE

use Illuminate\Support\Arr;
use Narsil\Localization\Interfaces\IHasTranslations;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class TranslationObserver
{
    #region PUBLIC METHODS

    /**
     * @param Translation $translation
     *
     * @return void
     */
    public function saved(Translation $translation): void
    {
        $attributes = request()->get(IHasTranslations::ATTRIBUTE_TRANSLATIONS . '.' . Translation::RELATIONSHIP_VALUES . '.' . Translation::RELATIONSHIP_VALUES, []);

        foreach ($attributes as $key => $value)
        {
            TranslationValue::updateOrCreate([
                TranslationValue::KEY_ID => $translation->{Translation::ID},
                TranslationValue::LANGUAGE_ID => $key,
            ], [
                TranslationValue::VALUE => $value,
            ]);
        }
    }

    /**
     * @param Translation $translation
     *
     * @return void
     */
    public function saving(Translation $translation): void
    {
        $attributes = request()->get(IHasTranslations::ATTRIBUTE_TRANSLATIONS . '.' . Translation::RELATIONSHIP_VALUES, []);

        if ($defaultValue = Arr::get($attributes, Translation::DEFAULT_VALUE))
        {
            $translation->{Translation::DEFAULT_VALUE} = $defaultValue;
        }
    }

    #endregion
}
