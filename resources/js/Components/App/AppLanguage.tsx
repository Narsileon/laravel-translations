import { ChevronDown } from "lucide-react";
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
	chevron?: boolean;
	format?: "long" | "short";
	languages: LanguageModel[];
}

const AppLanguage = ({
	chevron = false,
	children,
	className,
	languages,
	format = "short",
	...props
}: AppLanguageProps) => {
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
				<DropdownMenuTrigger
					className='group'
					asChild={true}
				>
					<Button
						className={cn("gap-x-1", className)}
						{...props}
					>
						{children}
						{label}
						{chevron ? (
							<ChevronDown className='h-5 w-5 transition-transform duration-200 group-aria-expanded:rotate-180' />
						) : null}
					</Button>
				</DropdownMenuTrigger>
			</TooltipWrapper>

			<DropdownMenuContent>
				{languages.map((language, index) => {
					return (
						<DropdownMenuItem
							active={language.locale === locale}
							asChild={true}
							key={index}
						>
                            <Link
                                as='button'
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
