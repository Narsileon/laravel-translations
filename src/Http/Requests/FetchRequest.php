<?php

namespace Narsil\Localization\Http\Requests;

#region USE

use Narsil\Forms\Http\Requests\AbstractFormRequest;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class FetchRequest extends AbstractFormRequest
{
    #region CONSTANTS

    public const SEARCH = 'search';

    #endregion

    #region PUBLIC METHODS

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::SEARCH => [
                self::REQUIRED,
                self::TYPE_STRING,
            ],
        ];
    }

    #endregion
}
