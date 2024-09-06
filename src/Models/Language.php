<?php

namespace Narsil\Localization\Models;

#region USE

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Narsil\Localization\Casts\TransAttribute;
use Narsil\Localization\Interfaces\IHasTranslations;
use Narsil\Localization\Traits\HasTranslations;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class Language extends Model implements IHasTranslations
{
    use HasTranslations;

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
            self::LABEL => TransAttribute::class,
        ];

        $this->fillable = [
            self::ACTIVE,
            self::LABEL,
            self::LOCALE,
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
    final public const LABEL = 'label';
    /**
     * @var string
     */
    final public const LOCALE = 'locale';

    /**
     * @var string
     */
    final public const TABLE = 'languages';

    #endregion

    #region SCOPES

    /**
     * @param Builder $query
     *
     * @return void
     */
    final public function scopeOptions(Builder $query): void
    {
        $query
            ->select([
                self::ID,
                self::LABEL,
                self::LOCALE,
            ])
            ->where(self::ACTIVE, true);
    }

    #endregion

    #region PUBLIC METHODS

    /**
     * @return Language|null
     */
    final public static function fallbackLocale(): Language|null
    {
        $locale = App::getFallbackLocale();

        $language = Language::firstWhere([
            self::LOCALE => $locale,
        ]);

        return $language;
    }

    /**
     * @return Language|null
     */
    final public static function locale(): Language|null
    {
        $locale = App::getLocale();

        $language = Language::firstWhere([
            self::LOCALE => $locale,
        ]);

        return $language;
    }

    #endregion
}
