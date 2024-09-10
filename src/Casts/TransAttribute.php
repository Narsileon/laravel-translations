<?php

namespace Narsil\Localization\Casts;

#region USE

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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

        $translation = null;

        if (is_array($value))
        {
            if ($id = Arr::get($value, Translation::ID))
            {
                $translation = static::$translationRepository->getById($id);

                if ($translation)
                {
                    $translation = $this->updateTranslation($translation, $value);
                }
                else
                {
                    $translation = $this->createTranslation($value);
                }
            }

            else
            {
                $translation = $this->createTranslation($value);
            }

            static::$translationRepository->flush();
        }
        else
        {
            $translation = static::$translationRepository->getByKey($value);
        }

        if (!$translation)
        {
            $translation = Translation::create([
                Translation::DEFAULT_VALUE => $value,
            ]);

            static::$translationRepository->flush();
        }

        return (string)($translation->{Translation::ID});
    }

    #endregion

    #region PRIVATE METHODS

    private function createTranslation(array $attributes): Translation
    {
        $translation = Translation::create([
            Translation::DEFAULT_VALUE => Arr::get($attributes, Translation::DEFAULT_VALUE),
        ]);

        foreach (Arr::get($attributes, Translation::RELATIONSHIP_VALUES, []) as $key => $value)
        {
            TranslationValue::create([
                TranslationValue::KEY_ID => $translation->{Translation::ID},
                TranslationValue::LANGUAGE_ID => $key,
                TranslationValue::VALUE => $value,
            ]);
        }

        return $translation;
    }

    private function updateTranslation(Translation $translation, array $attributes): Translation
    {
        $translation->update([
            Translation::DEFAULT_VALUE => Arr::get($attributes, Translation::DEFAULT_VALUE),
        ]);

        foreach (Arr::get($attributes, Translation::RELATIONSHIP_VALUES, []) as $key => $value)
        {
            TranslationValue::updateOrCreate([
                TranslationValue::KEY_ID => $translation->{Translation::ID},
                TranslationValue::LANGUAGE_ID => $key,
            ], [
                TranslationValue::VALUE => $value,
            ]);
        }

        return $translation;
    }

    #endregion
}
