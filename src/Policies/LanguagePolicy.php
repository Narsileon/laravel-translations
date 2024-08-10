<?php

namespace Narsil\Localization\Policies;

#region USE

use Narsil\Localization\Models\Language;
use Narsil\Policies\Policies\AbstractPolicy;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class LanguagePolicy extends AbstractPolicy
{
    #region CONSTRUCTOR

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct(Language::class);
    }

    #endregion
}
