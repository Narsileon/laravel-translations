<?php

namespace Narsil\Localization\Commands;

#region USE

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Narsil\Localization\Enums\LocaleEnum;
use Narsil\Localization\Interfaces\ITranslationRepository;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

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
        app(ITranslationRepository::class)->flush();

        $locales = Config::get('narsil-localization.locales', array_map(fn($case) => $case->value, LocaleEnum::cases()));

        foreach ($locales as $locale)
        {
            $language = Language::firstOrCreate([
                Language::LOCALE => $locale,
            ]);

            $this->createLanguageTranslations($language);

            app(ITranslationRepository::class)->flush();

            $language->update([
                Language::LANGUAGE => $locale
            ]);
        }

        Artisan::call('cache:clear');

        $this->info('Translation tables have been successfully synced with the translation files.');
    }

    #endregion

    #region PRIVATE METHODS

    /**
     * @param Language $language
     *
     * @return void
     */
    private function createLanguageTranslations(Language $language): void
    {
        $translations = $this->getLocaleTranslations($language->{Language::LOCALE});

        foreach ($translations as $key => $value)
        {
            $entry = Translation::firstOrCreate([
                Translation::KEY => $key
            ]);

            $localization = TranslationValue::firstOrCreate([
                TranslationValue::KEY_ID => $entry->{Translation::ID},
                TranslationValue::LANGUAGE_ID => $language->{Language::ID},
            ], [
                TranslationValue::VALUE => $value
            ]);

            if (!$localization->{TranslationValue::VALUE})
            {
                $localization->update([
                    TranslationValue::VALUE => $value,
                ]);
            }
        }
    }

    /**
     * @param array<string,mixed>|string $translations
     * @param string $path
     * @param array<string,string> $flattenedTranslations
     *
     * @return array
     */
    private function flatTranslations(array|string $translations, string $path, array $flattenedTranslations = []): array
    {
        if (is_string($translations))
        {
            return [];
        }

        foreach ($translations as $key => $value)
        {
            if (is_array($value))
            {
                $flattenedTranslations = $this->flatTranslations($value, $path . '.' . $key, $flattenedTranslations);
            }

            else
            {
                $flattenedTranslations[$path . '.' . $key] = $value;
            }
        }

        return $flattenedTranslations;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    private function getLocaleTranslations(string $locale): array
    {
        $jsonTranslations = Lang::get('*', [], $locale);

        $keys = Config::get('narsil-localization.translations', []);

        $phpTranslations = [];

        foreach ($keys as $key)
        {
            $parts = explode('::', $key);
            $path = array_pop($parts);

            $phpTranslations += $this->flatTranslations(Lang::get($key, [], $locale), $path);
        }

        $translations = array_merge($phpTranslations, $jsonTranslations);

        return $translations;
    }

    #endregion
}
