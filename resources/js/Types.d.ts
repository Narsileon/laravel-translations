type LanguageModel = {
	active: boolean;
	created_at: string;
	id: number;
	language: string;
	locale: string;
	updated_at: string;
};

type TranslationModel = {
	active: boolean;
	created_at: string;
	default_value: string;
	id: number;
	key: string;
	updated_at: string;
};

type TranslationValueModel = {
	active: boolean;
	created_at: string;
	id: number;
	key_id: number;
	key: TranslationModel;
	language_id: number;
	language: LanguageModel;
	updated_at: string;
	value: string;
};
