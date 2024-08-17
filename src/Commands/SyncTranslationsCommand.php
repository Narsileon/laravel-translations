<?php

namespace Narsil\Localization\Commands;

#region USE

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Narsil\Localization\Constants\LocalizationConfig;
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
        $this->signature = 'narsil:sync-translations {--fresh}';
        $this->description = 'Syncs the translation tables with the translation files';

        parent::__construct();
    }

    #endregion

    #region PROPERTIES

    /**
     * @var Collection Collection of Language keyed by locale.
     */
    private Collection $languages;
    /**
     * @var Collection Collection of Translation keyed by key.
     */
    private Collection $translations;
    /**
     * @var Collection Collection of TranslationValue grouped by key_id.
     */
    private Collection $translationValues;

    #endregion

    #region PUBLIC METHODS

    /**
     * @return void
     */
    public function handle(): void
    {
        if ($this->option('fresh'))
        {
            Translation::query()->delete();
        }

        $this->languages = Language::all()->keyBy(Language::LOCALE);
        $this->translations = Translation::all()->keyBy(Translation::KEY);
        $this->translationValues = Translation::all()->groupBy(TranslationValue::KEY_ID);

        app(ITranslationRepository::class)->flush();

        $locales = Config::get(LocalizationConfig::LOCALES, array_map(fn($case) => $case->value, LocaleEnum::cases()));

        foreach ($locales as $locale)
        {
            $language = $this->getLanguage($locale);

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
            $translation = $this->getTranslation($key);
            $translationValue = $this->getTranslationValue($language, $translation, $value);
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
     * @return Language
     */
    private function getLanguage(string $locale): Language
    {
        $language = $this->languages->get($locale);

        if (!$language)
        {
            $language = Language::create([
                Language::LOCALE => $locale,
            ]);

            $this->languages->put($locale, $language);
        }

        return $language;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    private function getLocaleTranslations(string $locale): array
    {
        $jsonTranslations = Lang::get('*', [], $locale);

        $phpTranslations = [];

        $keys = Config::get(LocalizationConfig::TRANSLATIONS, []);

        foreach ($keys as $key)
        {
            $parts = explode('::', $key);
            $path = array_pop($parts);

            $phpTranslations += $this->flatTranslations(Lang::get($key, [], $locale), $path);
        }

        $translations = array_merge($phpTranslations, $jsonTranslations);

        return $translations;
    }

    /**
     * @param string $key
     *
     * @return Translation
     */
    private function getTranslation(string $key): Translation
    {
        $translation = $this->translations->get($key);

        if (!$translation)
        {
            $translation = Translation::create([
                Translation::KEY => $key
            ]);

            $this->translations->put($key, $translation);
        }

        return $translation;
    }

    /**
     * @param Language $language
     * @param Translation $translation
     * @param string $value
     *
     * @return Translation
     */
    private function getTranslationValue(Language $language, Translation $translation, string $value): TranslationValue
    {
        $translationValues = $this->translationValues->get($translation->{Translation::ID}, collect([]));

        $translationValue = $translationValues->where(TranslationValue::LANGUAGE_ID, $language->{Language::ID})
            ->first();

        if (!$translationValue)
        {
            $translationValue = TranslationValue::create([
                TranslationValue::KEY_ID => $translation->{Translation::ID},
                TranslationValue::LANGUAGE_ID => $language->{Language::ID},
                TranslationValue::VALUE => $value,
            ]);

            $translationValues->push($translationValue);

            $this->translationValues->put($translation->{Translation::ID}, $translationValues);
        }

        if (!$translationValue->{TranslationValue::VALUE} || !$translationValue->{TranslationValue::VALUE} !== $value)
        {
            $translationValue->update([
                TranslationValue::VALUE => $value,
            ]);
        }

        return $translationValue;
    }

    #endregion
}
