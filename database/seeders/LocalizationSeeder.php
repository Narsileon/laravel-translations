<?php

namespace Database\Seeders;

#region USE

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
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
final class LocalizationSeeder extends Seeder
{
    #region PUBLIC METHODS

    /**
     * @return void
     */
    public function run(): void
    {
        app(ITranslationRepository::class)->flush();

        $locales = Config::get('narsil.locales', array_map(fn($case) => $case->value, LocaleEnum::cases()));

        foreach ($locales as $locale)
        {
            $language = Language::firstOrCreate([
                Language::LOCALE => $locale,
            ]);

            $this->createLanguageTranslations($language);

            app(ITranslationRepository::class)->flush();

            $language->update([
                Language::LANGUAGE => "locales.$locale"
            ]);
        }
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
     * @param array $translations
     * @param array $formatedTranslations
     * @param string $path
     *
     * @return array
     */
    private function formatTranslations(array $translations, array $formatedTranslations = [], string $path = null): array
    {
        foreach ($translations as $key => $value)
        {
            if (is_array($value))
            {
                $formatedTranslations = $this->formatTranslations($value, $formatedTranslations, $path == null ? $key : $path . '.' . $key);
            }

            else
            {
                $formatedTranslations[$path ? $path . '.' . $key : $key] = $value;
            }
        }

        return $formatedTranslations;
    }

    /**
     * @param string $locale
     * @param string $path
     * @param string $namespace
     *
     * @return array
     */
    private function getFileTranslations(string $locale, string $path, string $namespace = '')
    {
        $translations = [];

        if (file_exists($path))
        {
            $files = File::files($path);

            foreach ($files as $file)
            {
                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                $translations[$fileName] = Lang::get($namespace . $fileName, [], $locale);
            }
        }

        $formatedTranslations = $this->formatTranslations($translations);

        return $formatedTranslations;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    private function getLocaleTranslations(string $locale): array
    {
        $jsonTranslations = Lang::get('*', [], $locale);

        $narsilTranslations = $this->getFileTranslations(
            locale: $locale,
            path: base_path('/vendor/narsil/narsil-framework/lang/' . $locale),
            namespace: 'narsil-framework::'
        );

        $appTranslations = $this->getFileTranslations(
            locale: $locale,
            path: lang_path($locale),
        );

        $translations = array_merge($narsilTranslations, $appTranslations, $jsonTranslations);

        return $translations;
    }

    #endregion
}
