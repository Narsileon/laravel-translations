import { cn } from "@narsil-ui/Components";
import { LanguageModel } from "@narsil-localization/Types";
import { Link } from "@inertiajs/react";
import { upperCase } from "lodash";
import { useTranslationsStore } from "@narsil-localization/Stores/translationStore";
import Button, { ButtonProps } from "@narsil-ui/Components/Button/Button";
import DropdownMenu from "@narsil-ui/Components/DropdownMenu/DropdownMenu";
import DropdownMenuContent from "@narsil-ui/Components/DropdownMenu/DropdownMenuContent";
import DropdownMenuItem from "@narsil-ui/Components/DropdownMenu/DropdownMenuItem";
import DropdownMenuTrigger from "@narsil-ui/Components/DropdownMenu/DropdownMenuTrigger";
import TooltipWrapper from "@narsil-ui/Components/Tooltip/TooltipWrapper";

export interface AppLanguageProps extends ButtonProps {
	format?: "long" | "short";
	languages: LanguageModel[];
}

const AppLanguage = ({ languages, format = "short", ...props }: AppLanguageProps) => {
	const { locale, trans } = useTranslationsStore();

	const label = (function () {
		let value;

		switch (format) {
			case "long":
				value = trans(locale);
				break;
			default:
				value = upperCase(locale);
				break;
		}

		return value;
	})();

	return (
		<DropdownMenu>
			<TooltipWrapper tooltip={trans("language")}>
				<DropdownMenuTrigger asChild={true}>
					<Button {...props}>{label}</Button>
				</DropdownMenuTrigger>
			</TooltipWrapper>

			<DropdownMenuContent>
				{languages.map((language, index) => {
					return (
						<DropdownMenuItem
							className={cn({ "text-primary font-semibold": language.locale === locale })}
							asChild={true}
							key={index}
						>
							<Link
								href={route("locale")}
								method='patch'
								data={{
									locale: language.locale,
								}}
							>
								{language.language}
							</Link>
						</DropdownMenuItem>
					);
				})}
			</DropdownMenuContent>
		</DropdownMenu>
	);
};

export default AppLanguage;
