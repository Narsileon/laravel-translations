<?php

namespace Narsil\Localization\Traits;

#region USE

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Narsil\Localization\Casts\TransAttribute;
use Narsil\Localization\Http\Resources\TranslationResource;
use Narsil\Localization\Models\Translation;

#endregion

trait HasTranslations
{
    #region ATTRIBUTES

    /**
     * @return Attribute
     */
    protected function translations(): Attribute
    {
        return new Attribute(
            get: function ()
            {
                $attributes = $this->getTransAttributes();

                $translationIds = Arr::where(array_values($attributes), function ($value)
                {
                    return is_int($value);
                });

                $translations = $this->getTranslations($translationIds);

                return array_map(function ($value) use ($translations)
                {
                    return new TranslationResource($translations->get($value));
                }, $attributes);
            }
        );
    }

    #endregion

    #region PRIVATE METHODS

    /**
     * @return array
     */
    private function getTransAttributes(): array
    {
        $attributes = array_keys(array_filter($this->casts, function ($cast)
        {
            return $cast === TransAttribute::class;
        }));

        return array_combine($attributes, array_map(function ($field)
        {
            return $this->getRawOriginal($field);
        }, $attributes));
    }

    /**
     * @param array $translationIds
     *
     * @return Collection
     */
    private function getTranslations(array $translationIds): Collection
    {
        return Translation::query()
            ->whereIn(Translation::ID, $translationIds)
            ->get()
            ->keyBy(Translation::ID);
    }

    #endregion
}
