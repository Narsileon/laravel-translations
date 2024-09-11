<?php

namespace Narsil\Localization\Http\Forms;

#region USE

use Illuminate\Http\Request;
use Narsil\Forms\Builder\AbstractForm;
use Narsil\Forms\Builder\AbstractFormNode;
use Narsil\Forms\Builder\Elements\FormCard;
use Narsil\Forms\Builder\Inputs\FormString;
use Narsil\Forms\Builder\Inputs\FormTrans;
use Narsil\Localization\Http\Resources\TranslationResource;
use Narsil\Localization\Interfaces\IHasTranslations;
use Narsil\Localization\Models\Translation;

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

        $attributes[IHasTranslations::ATTRIBUTE_TRANSLATIONS] = [
            Translation::RELATIONSHIP_VALUES => new TranslationResource($this),
        ];

        $attributes[Translation::RELATIONSHIP_VALUES] = null;

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
                    (new FormString(Translation::KEY)),
                    (new FormTrans(Translation::RELATIONSHIP_VALUES))
                        ->label('validation.attributes.value')
                        ->required(),
                ]),
        ];
    }

    #endregion
}
