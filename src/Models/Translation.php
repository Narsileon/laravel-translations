<?php

namespace Narsil\Localization\Models;

#region USE

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class Translation extends Model
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

        $this->casts = [
            self::ACTIVE => 'boolean',
        ];

        $this->fillable = [
            self::ACTIVE,
            self::DEFAULT_VALUE,
            self::KEY,
        ];

        $this->with = [
            self::RELATIONSHIP_VALUES,
        ];

        parent::__construct($attributes);
    }

    #endregion

    #region CONSTANTS

    /**
     * @var string
     */
    final public const ACTIVE = 'active';
    /**
     * @var string
     */
    final public const ID = 'id';
    /**
     * @var string
     */
    final public const DEFAULT_VALUE = 'default_value';
    /**
     * @var string
     */
    final public const KEY = 'key';

    /**
     * @var string
     */
    final public const RELATIONSHIP_VALUES = 'values';

    /**
     * @var string
     */
    final public const TABLE = 'translations';

    #endregion

    #region RELATIONSHIP

    /**
     * @return HasMany
     */
    final public function values(): HasMany
    {
        return $this->hasMany(
            TranslationValue::class,
            TranslationValue::KEY_ID,
            self::ID
        );
    }

    #endregion

    #region SCOPES

    /**
     * @param Builder $query
     *
     * @return void
     */
    final public function scopeDictionary(Builder $query, int $languageId): void
    {
        $query
            ->with([self::RELATIONSHIP_VALUES => function ($query) use ($languageId)
            {
                $query->where(TranslationValue::LANGUAGE_ID, '=', $languageId);
            }])
            ->where(self::ACTIVE, '=', true)
            ->select([
                self::ID,
                self::KEY,
            ]);
    }

    #endregion
}
