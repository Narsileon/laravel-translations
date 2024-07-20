<?php

namespace Narsil\NarsilFramework;

#region USE

use Illuminate\Support\ServiceProvider;
use Narsil\Localizations\Commands\SyncTranslationsCommand;
use Narsil\Localizations\Interfaces\ITranslationRepository;
use Narsil\NarsilFramework\Repositories\TranslationRepository;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class NarsilFrameworkServiceProvider extends ServiceProvider
{
    #region PUBLIC METHODS

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->bootCommands();
        $this->bootMigrations();
        $this->bootRoutes();
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ITranslationRepository::class, TranslationRepository::class);
    }

    #endregion

    #region PRIVATE METHODS

    /**
     * @return void
     */
    private function bootCommands(): void
    {
        $this->commands([
            SyncTranslationsCommand::class,
        ]);
    }

    /**
     * @return void
     */
    private function bootMigrations(): void
    {
        $this->loadMigrationsFrom([
            __DIR__ . '/../database/migrations',
        ]);
    }

    /**
     * @return void
     */
    private function bootRoutes(): void
    {
        $this->loadRoutesFrom([
            __DIR__ . '/../routes/web.php'
        ]);
    }

    #endregion
}
