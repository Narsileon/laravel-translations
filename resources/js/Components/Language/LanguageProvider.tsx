import * as React from "react";

type LanguageProviderState = {
	contextLocale: string;
};

type LanguageProviderAction = {
	setContextLocale: (locale: string) => void;
};

type LanguageProviderType = LanguageProviderState & LanguageProviderAction;

const LanguageProviderContext = React.createContext<LanguageProviderType>({} as LanguageProviderType);

export interface LanguageProviderProps {
	children: React.ReactNode;
	initialLocale: string;
}

const LanguageProvider = ({ children, initialLocale }: LanguageProviderProps) => {
	const [contextLocale, setContextLocale] = React.useState<string>(initialLocale);

	const value = {
		contextLocale: contextLocale,
		setContextLocale: setContextLocale,
	};

	React.useEffect(() => {
		setContextLocale(initialLocale);
	}, [initialLocale]);

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
