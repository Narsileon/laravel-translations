<?php

namespace Narsil\Localization\Http\Controllers;

#region USE

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Narsil\Localization\Http\Requests\FetchRequest;
use Narsil\Localization\Http\Resources\TranslationResource;
use Narsil\Localization\Models\Language;
use Narsil\Localization\Models\Translation;
use Narsil\Localization\Models\TranslationValue;

#endregion

/**
 * @version 1.0.0
 *
 * @author Jonathan Rigaux
 */
class TranslationFetchController extends Controller
{
    #region PUBLIC METHODS

    /**
     * @return AnonymousResourceCollection
     */
    public function __invoke(FetchRequest $request): AnonymousResourceCollection
    {
        $search = $request->validated(FetchRequest::SEARCH);

        $language = Language::locale();

        $translations = Translation::query()
            ->where(Translation::ACTIVE, true)
            ->whereHas(Translation::RELATIONSHIP_VALUES, function ($query) use ($search, $language)
            {
                $query
                    ->where(TranslationValue::LANGUAGE_ID, $language->{Language::ID})
                    ->where(TranslationValue::VALUE, 'like', "%$search%");
            })
            ->get();

        return TranslationResource::collection($translations);
    }

    #endregion
}
