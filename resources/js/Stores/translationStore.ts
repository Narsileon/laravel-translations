import { create } from "zustand";
import { LanguageModel } from "@narsil-localization/Types";
import { upperFirst } from "lodash";
import { z, ZodErrorMap, ZodIssueCode } from "zod";

export type Translation = {
	id: number;
	key: string | null;
	value: string;
};

export type TransOptions = {
	replacements?: Record<string, string | number>;
	upperfirst?: boolean;
};

export type TranslationStoreState = {
	languages: LanguageModel[];
	locale: string;
	translations: { [key: string]: Translation };
};

export type TranslationStoreAction = {
	getLanguage: () => LanguageModel;
	setLanguages: (languages: LanguageModel[]) => void;
	setLocale: (locale: string) => void;
	setTranslations: (translations: TranslationStoreState["translations"]) => void;
	trans: (key: string, options?: TransOptions) => string;
};

export type TranslationStoreType = TranslationStoreState & TranslationStoreAction;

const defaultState: TranslationStoreState = {
	languages: [],
	locale: "",
	translations: {},
};

const errorMap = (trans: TranslationStoreAction["trans"]): ZodErrorMap => {
	return (issue, ctx) => {
		const attribute = trans(`validation.attributes.${issue.path[issue.path.length - 1]}`);

		let message;

		switch (issue.code) {
			case ZodIssueCode.custom:
				message = ctx.defaultError;
				break;
			case ZodIssueCode.invalid_date:
				message = trans("validation.date", {
					replacements: {
						attribute: attribute,
					},
				});
				break;
			case ZodIssueCode.invalid_enum_value:
				message = trans("validation.enum", {
					replacements: {
						attribute: attribute,
					},
				});
				break;
			case ZodIssueCode.invalid_string:
				if (issue.validation === "email") {
					message = trans("validation.email", {
						replacements: {
							attribute: attribute,
						},
					});
				} else if (issue.validation === "regex") {
					message = trans("validation.regex", {
						replacements: {
							attribute: attribute,
						},
					});
				} else if (issue.validation === "url") {
					message = trans("validation.url", {
						replacements: {
							attribute: attribute,
						},
					});
				}
				break;
			case ZodIssueCode.invalid_type:
				if (issue.expected === "array") {
					message = trans("validation.array", {
						replacements: {
							attribute: attribute,
						},
					});
				} else if (issue.expected === "number") {
					message = trans("validation.numeric", {
						replacements: {
							attribute: attribute,
						},
					});
				} else if (issue.expected === "string") {
					message = trans("validation.string", {
						replacements: {
							attribute: attribute,
						},
					});
				}
				break;
			case ZodIssueCode.too_big:
				if (issue.maximum !== undefined) {
					if (issue.type === "array") {
						message = trans("validation.max.array", {
							replacements: {
								attribute: attribute,
								max: `${issue.maximum}`,
							},
						});
					} else if (issue.type === "number") {
						message = trans("validation.max.numeric", {
							replacements: {
								attribute: attribute,
								max: `${issue.maximum}`,
							},
						});
					} else if (issue.type === "string") {
						message = trans("validation.max.string", {
							replacements: {
								attribute: attribute,
								max: `${issue.maximum}`,
							},
						});
					}
				}
				break;
			case ZodIssueCode.too_small:
				if (issue.minimum !== undefined) {
					if (issue.type === "array") {
						message = trans("validation.min.array", {
							replacements: {
								attribute: attribute,
								min: `${issue.minimum}`,
							},
						});
					} else if (issue.type === "number") {
						message = trans("validation.min.numeric", {
							replacements: {
								attribute: attribute,
								min: `${issue.minimum}`,
							},
						});
					} else if (issue.type === "string") {
						message = trans("validation.min.string", {
							replacements: {
								attribute: attribute,
								min: `${issue.minimum}`,
							},
						});
					}
				}
				break;
			default:
				message = ctx.defaultError;
		}

		if (!message) {
			message = ctx.defaultError;
		}

		return { message };
	};
};

export const useTranslationsStore = create<TranslationStoreType>((set, get) => ({
	...defaultState,

	getLanguage: () => {
		return get().languages.find((x) => x.locale === get().locale) as LanguageModel;
	},
	setLanguages: (languages) =>
		set({
			languages: languages,
		}),
	setLocale: (locale) =>
		set({
			locale: locale,
		}),
	setTranslations: (translations) =>
		set((state) => {
			const newState = {
				...state,
				translations: translations,
			};

			z.setErrorMap(errorMap(newState.trans));

			return newState;
		}),
	trans: (key, options) => {
		let text = get().translations[key]?.value ?? key.split(".").pop();

		if (options?.replacements) {
			Object.entries(options?.replacements).map(([replacementKey, replacementValue]) => {
				if (text.includes(replacementKey)) {
					text = text.replace(`:${replacementKey}`, `${replacementValue}`);
				} else if (text.includes(upperFirst(replacementKey))) {
					text = text.replace(`:${upperFirst(replacementKey)}`, upperFirst(`${replacementValue}`));
				}
			});
		}

		return options?.upperfirst === false ? text : upperFirst(text);
	},
}));
