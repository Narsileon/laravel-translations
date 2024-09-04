import * as React from "react";
import type { LanguageModel } from "@narsil-localization/Types";

type LanguageProviderState = {
	contextLanguage: LanguageModel;
};

type LanguageProviderAction = {
	setContextLanguage: (language: LanguageModel) => void;
};

type LanguageProviderType = LanguageProviderState & LanguageProviderAction;

const LanguageProviderContext = React.createContext<LanguageProviderType>({} as LanguageProviderType);

export interface LanguageProviderProps {
	children: React.ReactNode;
	defaultLanguage: LanguageModel;
}

const LanguageProvider = ({ children, defaultLanguage }: LanguageProviderProps) => {
	const [contextLanguage, setContextLanguage] = React.useState<LanguageModel>(defaultLanguage);

	const value = {
		contextLanguage: contextLanguage,
		setContextLanguage: setContextLanguage,
	};

	return <LanguageProviderContext.Provider value={value}>{children}</LanguageProviderContext.Provider>;
};

export const useLanguageContext = () => {
	const context = React.useContext(LanguageProviderContext);

	if (context === undefined) {
		throw new Error("useLanguage must be used within a <LanguageProvider />");
	}

	return context;
};

export default LanguageProvider;
