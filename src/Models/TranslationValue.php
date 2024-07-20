<?php

namespace Narsil\Localizations\Models;

#region USE

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class TranslationValue extends Model
{
    #region CONSTRUCTOR

    /**
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = self::TABLE;

        $this->fillable = [
            self::KEY_ID,
            self::LANGUAGE_ID,
            self::VALUE,
        ];

        parent::__construct($attributes);
    }

    #endregion

    #region CONSTANTS

    /**
     * @var string
     */
    final public const ID = 'id';
    /**
     * @var string
     */
    final public const KEY_ID = 'key_id';
    /**
     * @var string
     */
    final public const LANGUAGE_ID = 'language_id';
    /**
     * @var string
     */
    final public const VALUE = 'value';

    /**
     * @var string
     */
    final public const RELATIONSHIP_KEY = 'key';
    /**
     * @var string
     */
    final public const RELATIONSHIP_LANGUAGE = 'language';

    /**
     * @var string
     */
    final public const TABLE = 'translation_values';

    #endregion

    #region RELATIONSHIPS

    /**
     * @return BelongsTo
     */
    final public function key(): BelongsTo
    {
        return $this->belongsTo(
            Translation::class,
            self::KEY_ID,
            Translation::ID
        );
    }

    /**
     * @return BelongsTo
     */
    final public function language(): BelongsTo
    {
        return $this->belongsTo(
            Language::class,
            self::LANGUAGE_ID,
            Language::ID
        );
    }

    #endregion
}
