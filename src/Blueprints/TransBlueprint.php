<?php

namespace Narsil\Localizations\Blueprints;

#region USE

use Illuminate\Database\Schema\Blueprint;
use Narsil\Localizations\Models\Translation;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class TransBlueprint
{
    #region PUBLIC METHODS

    /**
     * @param Blueprint $table
     * @param string $column
     *
     * @return void
     */
    public static function define(Blueprint $table, string $column): void
    {
        $column = $table->foreignId($column)
            ->nullable()
            ->constrained(Translation::TABLE, Translation::ID)
            ->nullOnDelete();
    }

    #endregion
}
