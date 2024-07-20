<?php

#region USE

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Narsil\Localizations\Models\Language;
use Narsil\Localizations\Models\Translation;
use Narsil\Localizations\Models\TranslationValue;

#endregion

return new class extends Migration
{
    #region MIGRATIONS

    /**
     * @return void
     */
    public function up(): void
    {
        $this->createTranslationsTable();
        $this->createLanguagesTable();
        $this->createTranslationValuesTable();

        Artisan::call('narsil:sync-translations');
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(TranslationValue::TABLE);
        Schema::dropIfExists(Language::TABLE);
        Schema::dropIfExists(Translation::TABLE);
    }

    #endregion

    #region TABLES

    /**
     * @return void
     */
    private function createTranslationsTable(): void
    {
        if (Schema::hasTable(Translation::TABLE))
        {
            return;
        }

        Schema::create(Translation::TABLE, function (Blueprint $table)
        {
            $table->resource();

            $table->string(Translation::KEY)
                ->unique()
                ->nullable();
            $table->text(Translation::DEFAULT_VALUE)
                ->nullable();
        });
    }

    /**
     * @return void
     */
    private function createTranslationValuesTable(): void
    {
        if (Schema::hasTable(TranslationValue::TABLE))
        {
            return;
        }

        Schema::create(TranslationValue::TABLE, function (Blueprint $table)
        {
            $table->resource();

            $table->foreignId(TranslationValue::KEY_ID)
                ->constrained(Translation::TABLE, Translation::ID)
                ->cascadeOnDelete();
            $table->foreignId(TranslationValue::LANGUAGE_ID)
                ->constrained(Language::TABLE, Language::ID)
                ->cascadeOnDelete();
            $table->text(TranslationValue::VALUE);
        });
    }

    /**
     * @return void
     */
    private function createLanguagesTable(): void
    {
        if (Schema::hasTable(Language::TABLE))
        {
            return;
        }

        Schema::create(Language::TABLE, function (Blueprint $table)
        {
            $table->resource();

            $table->trans(Language::LANGUAGE);
            $table->string(Language::LOCALE)
                ->unique();
        });
    }

    #endregion
};
