<?php

namespace Narsil\Localization\Http\Resources;

#region USE

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Narsil\Localization\Models\Translation;

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

            Translation::RELATIONSHIP_VALUES => TranslationValueResource::collection($this->{Translation::RELATIONSHIP_VALUES}),
        ];
    }

    #endregion
}
