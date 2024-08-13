<?php

namespace Narsil\Localization;

#region USE

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Narsil\Localization\Blueprints\TransBlueprint;
use Narsil\Localization\Commands\SyncTranslationsCommand;
use Narsil\Localization\Interfaces\ITranslationRepository;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Policies\LanguagePolicy;
use Narsil\Localization\Policies\TranslationPolicy;
use Narsil\Localization\Repositories\TranslationRepository;
use Narsil\Localization\Services\LocalizationLoaderService;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
final class NarsilLocalizationServiceProvider extends ServiceProvider
{
    #region PUBLIC METHODS

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->bootBlueprints();
        $this->bootCommands();
        $this->bootMigrations();
        $this->bootPolicies();
        $this->bootPublishes();
        $this->bootRoutes();
        $this->bootTranslations();
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
    private function bootBlueprints(): void
    {
        Blueprint::macro('trans', function (string $column, bool $unique = false)
        {
            TransBlueprint::define($this, $column, $unique);
        });
    }

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
    private function bootPolicies(): void
    {
        Gate::policy(Language::class, LanguagePolicy::class);
        Gate::policy(Translation::class, TranslationPolicy::class);
    }

    /**
     * @return void
     */
    private function bootPublishes(): void
    {
        $this->publishes([
            __DIR__ . './Config' => config_path(),
        ], 'config');
    }

    /**
     * @return void
     */
    private function bootRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    /**
     * @return void
     */
    private function bootTranslations(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../lang', 'localization');
    }

    #endregion
}
