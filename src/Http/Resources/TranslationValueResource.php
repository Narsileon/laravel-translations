<?php

namespace Narsil\Localization\Http\Resources;

#region USE

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class TranslationValueResource extends JsonResource
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
            $this->{TranslationValue::RELATIONSHIP_LANGUAGE}->{Language::LOCALE} => $this->{TranslationValue::VALUE}
        ];
    }

    #endregion
}
