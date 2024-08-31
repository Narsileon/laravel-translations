<?php

namespace Narsil\Localization\Constants;

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
abstract class LocalizationConfig
{
    #region CONSTANTS

    /**
     * @var string List of available locales.
     */
    final public const LOCALES = 'narsil-localization.locales';
    /**
     * @var string List of translation namespaces.
     */
    final public const TRANSLATIONS = 'narsil-localization.translations';

    #endregion
}
