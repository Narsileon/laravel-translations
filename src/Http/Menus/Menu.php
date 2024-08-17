<?php

namespace Narsil\Localization\Http\Menus;

#region USE

use Narsil\Menus\Enums\VisibilityEnum;
use Narsil\Menus\Http\Menus\AbstractMenu;
use Narsil\Menus\Models\MenuNode;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class Menu extends AbstractMenu
{
    #region PUBLIC METHODS

    /**
     * @return array
     */
    public static function getBackendMenu(): array
    {
        return [
            [
                MenuNode::LABEL => 'Languages',
                MenuNode::URL => '/backend/languages',
                MenuNode::VISIBILITY => VisibilityEnum::AUTH->value,
                MenuNode::RELATIONSHIP_ICON => 'lucide/languages',
            ],
            [
                MenuNode::LABEL => 'Translations',
                MenuNode::URL => '/backend/translations',
                MenuNode::VISIBILITY => VisibilityEnum::AUTH->value,
                MenuNode::RELATIONSHIP_ICON => 'lucide/book-a',
            ],
        ];
    }

    #endregion
}
