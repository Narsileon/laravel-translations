<?php

namespace Database\Seeders;

#region USE

use Illuminate\Database\Seeder;
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
     * @param array<string,mixed> $translations
     * @param string $path
     * @param array<string,string> $flattenedTranslations
     *
     * @return array
     */
    private function flatTranslations(array $translations, string $path, array $flattenedTranslations = []): array
    {
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

        $keys = Config::get('narsil.lang', [
            'narsil::locales',
        ]);

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
