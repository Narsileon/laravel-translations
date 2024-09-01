<?php

namespace Narsil\Localization\Http\Resources;

#region USE

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class TranslationResource extends JsonResource
{
    #region PUBLIC METHODS

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            Translation::DEFAULT_VALUE => $this->{Translation::DEFAULT_VALUE},
            Translation::ID => $this->{Translation::ID},

            Translation::RELATIONSHIP_VALUES => $this->getValues(),
        ];
    }

    #endregion

    #region PRIVATE METHODS

    /**
     * @return array
     */
    private function getValues(): array
    {
        $values = [];

        foreach ($this->values as $value)
        {
            $values[$value->{TranslationValue::RELATIONSHIP_LANGUAGE}->{Language::LOCALE}] = $value->{TranslationValue::VALUE};
        }

        return $values;
    }

    #endregion
}
