import { create } from 'zustand';
import { upperFirst } from 'lodash';

type Translation = {
	id: number;
	key: string | null;
	value: string;
};

type TransOptions = {
	replacements?: Record<string, string | number>;
	upperfirst?: boolean;
};

type State = {
	locale: string;
	translations: { [key: string]: Translation };
};

type Actions = {
	trans: (key: string, options?: TransOptions) => string;
	setLocale: (locale: string) => void;
	setTranslations: (translations: { [key: string]: Translation }) => void;
};

const initialState: State = {
	locale: '',
	translations: {},
};

export const useTranslationsStore = create<State & Actions>((set, get) => ({
	...initialState,
	trans: (key, options) => {
		let text = get().translations[key]?.value ?? key.split('.').pop();

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
	setLocale: (locale) =>
		set((state) => ({
			...state,
			locale: locale,
		})),
	setTranslations: (translations) =>
		set((state) => ({
			...state,
			translations: translations,
		})),
}));
