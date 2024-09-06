<?php

namespace Narsil\Localization\Http\Forms;

#region USE

use Narsil\Forms\Builder\AbstractForm;
use Narsil\Forms\Builder\AbstractFormNode;
use Narsil\Forms\Builder\Elements\FormCard;
use Narsil\Forms\Builder\Inputs\FormString;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class TranslationForm extends AbstractForm
{
    #region CONSTRUCTOR

    /**
     * @param mixed $resource
     *
     * @return void
     */
    public function __construct(mixed $resource)
    {
        parent::__construct($resource, 'Translation', 'translation');
    }

    #endregion

    #region PROTECTED METHODS

    /**
     * @return array<AbstractFormNode>
     */
    protected function getSchema(): array
    {
        return [
            (new FormCard('default'))
                ->children([
                    (new FormString(Translation::KEY))
                        ->required(),
                    (new FormString(Translation::DEFAULT_VALUE))
                        ->required(),
                    (new FormString(TranslationValue::VALUE))
                        ->required(),
                ]),
        ];
    }

    #endregion
}
