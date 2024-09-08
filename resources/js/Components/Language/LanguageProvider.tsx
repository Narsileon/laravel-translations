import { LanguageModel } from "@narsil-localization/Types";
import * as React from "react";

type LanguageProviderState = {
	contextLanguage: LanguageModel | null;
};

type LanguageProviderAction = {
	setContextLanguage: (language: LanguageModel | null) => void;
};

type LanguageProviderType = LanguageProviderState & LanguageProviderAction;

const LanguageProviderContext = React.createContext<LanguageProviderType>({} as LanguageProviderType);

export interface LanguageProviderProps {
	children: React.ReactNode;
}

const LanguageProvider = ({ children }: LanguageProviderProps) => {
	const [contextLanguage, setContextLanguage] = React.useState<LanguageModel | null>(null);

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
