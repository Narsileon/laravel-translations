<?php

namespace Narsil\NarsilFramework\Casts;

#region USE

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Narsil\Localizations\Interfaces\ITranslationRepository;
use Narsil\Localizations\Models\Translation;
use Narsil\Localizations\Models\TranslationValue;

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

        $translation = $translation->{Translation::RELATIONSHIP_VALUE}?->{TranslationValue::VALUE} ?? $translation->{Translation::DEFAULT_VALUE} ?? '';

        return $translation;
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
