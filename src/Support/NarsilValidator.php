<?php

namespace Narsil\Localization\Support;

#reghion USE

use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator as BaseValidator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class NarsilValidator extends BaseValidator
{
    #region CONSTANTS

    private const VALIDATION = 'validation';

    #endregion

    #region PUBLIC METHODS

    /**
     * @param array $data
     * @param array $rules
     *
     * @return Validator
     */
    final public static function make(array $data, array $rules): Validator
    {
        $messages = static::getMessages();

        return parent::make($data, $rules, $messages);
    }

    #endregion

    #region PROTECTED METHOD

    /**
     * @return array
     */
    protected static function getMessages(): array
    {
        $locale = App::getLocale();

        return Cache::rememberForever("validation_messages_{$locale}", function () use ($locale)
        {
            $messages = TranslationValue::query()
                ->with([
                    TranslationValue::RELATIONSHIP_KEY,
                    TranslationValue::RELATIONSHIP_LANGUAGE,
                ])
                ->whereRelation(TranslationValue::RELATIONSHIP_LANGUAGE, Language::LOCALE, '=', $locale)
                ->whereRelation(TranslationValue::RELATIONSHIP_KEY, Translation::KEY, 'like', self::VALIDATION . ".%")
                ->get()
                ->pluck(TranslationValue::VALUE, TranslationValue::RELATIONSHIP_KEY . '.' .  Translation::KEY)
                ->toArray();

            return static::formatMessages($messages);
        });
    }

    #endregion

    #region PRIVATE METHODS

    /**
     * @param array $messages
     *
     * @return array
     */
    private static function formatMessages(array $messages): array
    {
        $formattedMessages = [];

        foreach ($messages as $key => $value)
        {
            Arr::set($formattedMessages, $key, $value);
        }

        return $formattedMessages[self::VALIDATION] ?? [];
    }

    #endregion
}
