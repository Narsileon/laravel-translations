<?php

namespace Narsil\Localization\Interfaces;

#region USE

use Illuminate\Database\Eloquent\Collection;
use Narsil\Localization\Models\Translation;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
interface ITranslationRepository
{
    #region PUBLIC METHODS

    /**
     * @return void
     */
    public function flush(): void;

    /**
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * @param integer $id
     *
     * @return ?Translation
     */
    public function getById(int $id): ?Translation;

    /**
     * @param string $key
     *
     * @return ?Translation
     */
    public function getByKey(string $key): ?Translation;

    #endregion
}
