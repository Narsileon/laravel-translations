<?php

namespace Narsil\Localization\Support;

#reghion USE

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
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
class NarsilValidator
{
    #region CONSTRUCTOR

    /**
     * @param array $data
     * @param array $rules
     *
     * @return void
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    #endregion

    #region PROPERTIES

    /**
     * @var array
     */
    protected readonly array $data;
    /**
     * @var array
     */
    protected readonly array $rules;

    #endregion

    #region PUBLIC METHODS

    final public function validate(): ValidatorContract
    {
        $messages = $this->getMessages();

        return Validator::make($this->data, $this->rules, $messages);
    }

    #endregion

    #region PROTECTED METHOD

    /**
     * @return array
     */
    protected function getMessages(): array
    {
        $locale = App::getLocale();

        return Cache::rememberForever("validation_messages_{$locale}", function () use ($locale)
        {
            return TranslationValue::query()
                ->with([
                    TranslationValue::RELATIONSHIP_KEY,
                    TranslationValue::RELATIONSHIP_LANGUAGE,
                ])
                ->whereRelation(TranslationValue::RELATIONSHIP_LANGUAGE, Language::LOCALE, '=', $locale)
                ->whereRelation(TranslationValue::RELATIONSHIP_KEY, Translation::KEY, 'like', "validation.%")
                ->pluck(TranslationValue::VALUE, TranslationValue::RELATIONSHIP_KEY . '.' .  Translation::KEY)
                ->toArray();
        });
    }

    #endregion
}
