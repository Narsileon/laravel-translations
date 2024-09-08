import { cn } from "@narsil-ui/Components";
import { Globe } from "lucide-react";
import { LanguageModel } from "@narsil-localization/Types";
import { upperCase } from "lodash";
import { useLanguageContext } from "./LanguageProvider";
import { useTranslationsStore } from "@narsil-localization/Stores/translationStore";
import Button, { ButtonProps } from "@narsil-ui/Components/Button/Button";
import DropdownMenu from "@narsil-ui/Components/DropdownMenu/DropdownMenu";
import DropdownMenuContent from "@narsil-ui/Components/DropdownMenu/DropdownMenuContent";
import DropdownMenuItem from "@narsil-ui/Components/DropdownMenu/DropdownMenuItem";
import DropdownMenuTrigger from "@narsil-ui/Components/DropdownMenu/DropdownMenuTrigger";
import TooltipWrapper from "@narsil-ui/Components/Tooltip/TooltipWrapper";
import DropdownMenuSeparator from "@narsil-ui/Components/DropdownMenu/DropdownMenuSeparator";

export interface LanguageDropdownProps extends ButtonProps {
	languages: LanguageModel[];
}

const LanguageDropdown = ({ children, className, languages, ...props }: LanguageDropdownProps) => {
	const { trans } = useTranslationsStore();

	const { contextLanguage, setContextLanguage } = useLanguageContext();

	const standardLabel = trans("Default");

	return (
		<DropdownMenu>
			<TooltipWrapper tooltip={`${trans("language")} - ${contextLanguage?.label ?? standardLabel}`}>
				<DropdownMenuTrigger
					className='group'
					asChild={true}
				>
					<Button
						className={cn("gap-x-1", className)}
						size='icon'
						{...props}
					>
						{contextLanguage ? (
							upperCase(contextLanguage.locale)
						) : (
							<>
								<Globe className='h-6 w-6' />
								<span className='sr-only'>{standardLabel}</span>
							</>
						)}
					</Button>
				</DropdownMenuTrigger>
			</TooltipWrapper>
			<DropdownMenuContent>
				<DropdownMenuItem
					active={!contextLanguage}
					onClick={() => setContextLanguage(null)}
				>
					{standardLabel}
				</DropdownMenuItem>
				<DropdownMenuSeparator />
				{languages.map((language) => {
					return (
						<DropdownMenuItem
							active={language.locale === contextLanguage?.locale}
							onClick={() => setContextLanguage(language)}
							key={language.locale}
						>
							{language.label}
						</DropdownMenuItem>
					);
				})}
			</DropdownMenuContent>
		</DropdownMenu>
	);
};

export default LanguageDropdown;
