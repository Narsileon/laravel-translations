<?php

namespace Narsil\NarsilFramework\Repositories;

#region USE

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Narsil\Localizations\Interfaces\ITranslationRepository;
use Narsil\Localizations\Models\Translation;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class TranslationRepository implements ITranslationRepository
{
    #region CONSTANTS

    /**
     * @var string
     */
    private const CACHE_KEY = 'repositories:translations';

    #endregion

    #region PROPERTIES

    /**
     * @var Collection|null
     */
    private ?Collection $translationsById = null;
    /**
     * @var Collection|null
     */
    private ?Collection $translationsByKey = null;

    #endregion

    #region PUBLIC METHODS

    /**
     * @return void
     */
    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);

        $this->translationsById = null;
        $this->translationsByKey = null;
    }

    /**
     * @return Collection Returns a collection of all translations.
     */
    public function getAll(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, function ()
        {
            return Translation::all();
        });
    }

    /**
     * @param integer $id
     *
     * @return ?Translation Returns the translation for the giving id.
     */
    public function getById(int $id): ?Translation
    {
        if ($this->translationsById === null)
        {
            $this->translationsById = $this->getAll()->keyBy(Translation::ID);
        }

        return $this->translationsById->get($id);
    }

    /**
     * @param string $key
     *
     * @return ?Translation Returns the translation for the giving key.
     */
    public function getByKey(string $key): ?Translation
    {
        if ($this->translationsByKey === null)
        {
            $this->translationsByKey = $this->getAll()->keyBy(Translation::KEY);
        }

        return $this->translationsByKey->get($key);
    }

    #endregion
}
