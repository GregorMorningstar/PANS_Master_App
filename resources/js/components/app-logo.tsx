import React from 'react';
import { usePage } from '@inertiajs/react';
import AppLogoIcon from './app-logo-icon';

export default function AppLogo() {
    const page = usePage();
    const user = (page.props as any).auth?.user ?? {};
    const userName = user.name ?? 'Gość';

    return (
        <>
            <div className="flex aspect-square h-8 w-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
                <AppLogoIcon className="h-5 w-5 fill-current text-white dark:text-black" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 leading-tight font-semibold">
                    {userName}
                </span>
            </div>
        </>
    );
}
