import AppLogoIcon from './app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-md">
                <AppLogoIcon className="size-5" />
            </div>
            <div className="ml-1 grid flex-1 text-left">
                <span className="mb-0.5 truncate leading-none font-semibold text-sm">Bicol University<br/></span>
                <span className="mb-0.5 truncate leading-none text-xs opacity-70">OVPRDE</span>
            </div>
        </>
    );
}
