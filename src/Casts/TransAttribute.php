<?php

namespace Narsil\Localization\Casts;

#region USE

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Narsil\Localization\Interfaces\ITranslationRepository;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class TransAttribute implements CastsAttributes
{
    #region CONSTRUCTOR

    /**
     * @return void
     */
    public function __construct()
    {
        if (self::$translationRepository === null)
        {
            self::$translationRepository = app(ITranslationRepository::class);
        }
    }

    #endregion

    #region PROPERTIES

    /**
     * @var ITranslationRepository|null
     */
    protected static ITranslationRepository|null $translationRepository = null;

    #endregion

    #region PUBLIC METHODS

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     *
     * @return string
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (!is_numeric($value))
        {
            return $value ?? '';
        }

        $translation = static::$translationRepository->getById($value);

        if (!$translation)
        {
            return $value ?? '';
        }

        $translationValue = $translation->{Translation::RELATIONSHIP_VALUES}?->first(function ($value)
        {
            return $value->{TranslationValue::RELATIONSHIP_LANGUAGE}->{Language::LOCALE} === App::getLocale();
        });

        return $translationValue?->{TranslationValue::VALUE} ?? $translation->{Translation::DEFAULT_VALUE} ?? '';
    }

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     *
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (!$value || is_numeric($value))
        {
            return $value;
        }

        $translation = static::$translationRepository->getByKey($value);

        if (!$translation)
        {
            $translation = Translation::create([
                Translation::DEFAULT_VALUE => $value,
            ]);
        }

        return (string)$translation->{Translation::ID};
    }

    #endregion
}
