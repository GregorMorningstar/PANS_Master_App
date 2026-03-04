import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { SidebarMenu, SidebarMenuItem, SidebarMenuButton } from '@/components/ui/sidebar';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faListCheck } from '@fortawesome/free-solid-svg-icons';

export default function AdminSidebarMenu(): React.ReactElement {
  const page = usePage();
  const currentUrl = (page as any).url ?? (typeof window !== 'undefined' ? window.location.pathname : '');
  const isActive = (href: string) => currentUrl?.startsWith(href);

  return (
    <div className="w-full px-3">
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton asChild isActive={isActive('/admin/items/processes')}>
            <Link href="/admin/items/processes" className="flex items-center gap-2">
              <FontAwesomeIcon icon={faListCheck} className="h-4 w-4" />
              <span>Produkty z procesem</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </div>
  );
}
