import { ChevronDown } from "lucide-react";
import { cn } from "@narsil-ui/Components";
import { useLanguageContext } from "./LanguageProvider";
import { useTranslationsStore } from "@narsil-localization/Stores/translationStore";
import Button, { ButtonProps } from "@narsil-ui/Components/Button/Button";
import DropdownMenu from "@narsil-ui/Components/DropdownMenu/DropdownMenu";
import DropdownMenuContent from "@narsil-ui/Components/DropdownMenu/DropdownMenuContent";
import DropdownMenuItem from "@narsil-ui/Components/DropdownMenu/DropdownMenuItem";
import DropdownMenuTrigger from "@narsil-ui/Components/DropdownMenu/DropdownMenuTrigger";

export interface LanguageDropdownProps extends ButtonProps {}

const LanguageDropdown = ({ children, className, ...props }: LanguageDropdownProps) => {
	const { languages } = useTranslationsStore();

	const { contextLanguage, setContextLanguage } = useLanguageContext();

	return (
		<DropdownMenu>
			<DropdownMenuTrigger
				className='group'
				asChild={true}
			>
				<Button
					className={cn("gap-x-1", className)}
					{...props}
				>
					{contextLanguage.language}
					<ChevronDown className='h-5 w-5 transition-transform duration-200 group-aria-expanded:rotate-180' />
				</Button>
			</DropdownMenuTrigger>
			<DropdownMenuContent>
				{languages.map((language) => {
					return (
						<DropdownMenuItem
							active={language.locale === contextLanguage.locale}
							onClick={() => setContextLanguage(language)}
							key={language.locale}
						>
							{language.language}
						</DropdownMenuItem>
					);
				})}
			</DropdownMenuContent>
		</DropdownMenu>
	);
};

export default LanguageDropdown;
