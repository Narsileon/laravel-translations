<?php

namespace Narsil\Localization\Policies;

#region USE

use Narsil\Localization\Models\Translation;
use Narsil\Policies\Policies\AbstractPolicy;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class TranslationPolicy extends AbstractPolicy
{
    #region CONSTRUCTOR

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct(Translation::class);
    }

    #endregion
}
