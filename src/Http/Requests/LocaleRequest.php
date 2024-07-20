<?php

namespace Narsil\Localizations\Http\Requests;

#region USE

use Illuminate\Foundation\Http\FormRequest;
use Narsil\Localizations\Models\Language;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class LocaleRequest extends FormRequest
{
    #region PUBLIC METHODS

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            Language::LOCALE => [
                'required',
                'string',
            ],
        ];
    }

    #endregion
}
