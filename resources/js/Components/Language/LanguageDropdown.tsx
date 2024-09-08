import { cn } from "@narsil-ui/Components";
import { LanguageModel } from "@narsil-localization/Types";
import { upperCase } from "lodash";
import { useLanguageContext } from "./LanguageProvider";
import Button, { ButtonProps } from "@narsil-ui/Components/Button/Button";
import DropdownMenu from "@narsil-ui/Components/DropdownMenu/DropdownMenu";
import DropdownMenuContent from "@narsil-ui/Components/DropdownMenu/DropdownMenuContent";
import DropdownMenuItem from "@narsil-ui/Components/DropdownMenu/DropdownMenuItem";
import DropdownMenuTrigger from "@narsil-ui/Components/DropdownMenu/DropdownMenuTrigger";
import TooltipWrapper from "@narsil-ui/Components/Tooltip/TooltipWrapper";

export interface LanguageDropdownProps extends ButtonProps {
	languages: LanguageModel[];
}

const LanguageDropdown = ({ children, className, languages, ...props }: LanguageDropdownProps) => {
	const { contextLanguage, setContextLanguage } = useLanguageContext();

	return (
		<DropdownMenu>
			<TooltipWrapper tooltip={contextLanguage.label}>
				<DropdownMenuTrigger
					className='group'
					asChild={true}
				>
					<Button
						className={cn("gap-x-1", className)}
						size='icon'
						{...props}
					>
						{upperCase(contextLanguage.locale)}
					</Button>
				</DropdownMenuTrigger>
			</TooltipWrapper>
			<DropdownMenuContent>
				{languages.map((language) => {
					return (
						<DropdownMenuItem
							active={language.locale === contextLanguage.locale}
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
