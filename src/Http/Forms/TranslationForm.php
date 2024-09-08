<?php

namespace Narsil\Localization\Http\Forms;

#region USE

use Illuminate\Http\Request;
use Narsil\Forms\Builder\AbstractForm;
use Narsil\Forms\Builder\AbstractFormNode;
use Narsil\Forms\Builder\Elements\FormCard;
use Narsil\Forms\Builder\Inputs\FormString;
use Narsil\Forms\Builder\Inputs\FormTrans;
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

    #region PUBLIC METHODS

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        $attributes = parent::toArray($request);

        $attributes[Translation::RELATIONSHIP_VALUES] = $this->{Translation::RELATIONSHIP_VALUES}->pluck(
            TranslationValue::VALUE,
            TranslationValue::LANGUAGE_ID
        );

        return $attributes;
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
                    (new FormTrans(TranslationValue::VALUE))
                        ->required(),
                ]),
        ];
    }

    #endregion
}
