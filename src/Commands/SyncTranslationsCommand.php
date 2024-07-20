<?php

namespace Narsil\Localization\Commands;

#region USE

use Database\Seeders\LocalizationSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class SyncTranslationsCommand extends Command
{
    #region CONSTRUCTOR

    /**
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'narsil:sync-translations';
        $this->description = 'Syncs the translation tables with the translation files';

        parent::__construct();
    }

    #endregion

    #region PUBLIC METHODS

    /**
     * @return void
     */
    public function handle(): void
    {
        Artisan::call('db:seed', [
            '--class' => LocalizationSeeder::class,
            '--force' => true,
        ]);

        Artisan::call('cache:clear');

        $this->info('Translation tables have been successfully synced with the translation files.');
    }

    #endregion
}
