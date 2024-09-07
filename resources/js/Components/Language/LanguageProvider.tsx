import { LanguageModel } from "@narsil-localization/Types";
import * as React from "react";

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
	initialLanguage: LanguageModel;
}

const LanguageProvider = ({ children, initialLanguage }: LanguageProviderProps) => {
	const [contextLanguage, setContextLanguage] = React.useState<LanguageModel>(initialLanguage);

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
