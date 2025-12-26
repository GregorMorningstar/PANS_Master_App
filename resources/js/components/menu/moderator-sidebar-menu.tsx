import React, { useState, useEffect, useRef } from 'react';
import { usePage, Link } from '@inertiajs/react';
import { NavMain } from '@/components/nav-main';
import { LayoutGrid, ChevronDown } from 'lucide-react';
import type { NavItem as NavItemType } from '@/types';
import Barcode from 'react-barcode';

// FontAwesome
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
  faTachometerAlt,
  faUsers,
  faCogs,
  faChartLine,
  faPlus,
  faList,
  faExclamationTriangle,
  faSitemap,
  faWrench,
} from '@fortawesome/free-solid-svg-icons';

export default function ModeratorSidebarMenu(): React.ReactElement {
  const page = usePage();
  const user = (page.props as any).auth?.user ?? {};
  const barcodeValue =
    user.barcode ??
    user.barcode_value ??
    user.barcode_number ??
    (user.id ? String(user.id) : '—');

  const FaTachometerIcon = React.forwardRef<any, any>((props, ref) => (
    <FontAwesomeIcon ref={ref as any} {...props} icon={faTachometerAlt} className="h-4 w-4" />
  ));

  const items: NavItemType[] = [
    {
      title: 'Panel główny',
      href: '/moderator/dashboard',
      icon: FaTachometerIcon,
    },
  ];

  const [openKey, setOpenKey] = useState<'employees' | 'departaments' | 'performance' | 'machines' | 'incidents' | null>(null);
  const toggle = (key: 'employees' | 'departaments' | 'performance' | 'machines' | 'incidents') =>
    setOpenKey(prev => (prev === key ? null : key));

  const currentUrl =
    (page as any).url ?? (typeof window !== 'undefined' ? window.location.pathname : '');

  const isActive = (href: string) => currentUrl?.startsWith(href);

  const containerRef = useRef<HTMLDivElement | null>(null);
  const [isCollapsed, setIsCollapsed] = useState<boolean>(false);
  useEffect(() => {
    if (!containerRef.current) return;
    const el = containerRef.current;
    const threshold = 80; // px - adjust as needed (sidebar icon width ~48-64)
    const ro = new ResizeObserver(() => {
      const w = el.clientWidth;
      setIsCollapsed(w < threshold);
    });
    ro.observe(el);
    // initial check
    setIsCollapsed(el.clientWidth < threshold);
    return () => ro.disconnect();
  }, []);

  return (
    <div ref={containerRef} className="w-full flex flex-col items-center gap-3 px-3">
      {/* show barcode only when sidebar is wide enough */}
      {!isCollapsed && (
        <>
          <div className="w-full flex items-center justify-center">
            <div className="w-full">
              <Barcode
                value={barcodeValue}
                format="CODE128"
                renderer="svg"
                width={1.8}
                height={100}
                displayValue={false}
                margin={0}
                lineColor="#111827"
              />
            </div>
          </div>

          <div className="w-full text-center text-xs font-mono text-gray-700 break-words">
            {barcodeValue}
          </div>
        </>
      )}

      <div className="w-full mt-2">
        <NavMain items={items} />
      </div>

      <div className="w-full mt-2">
        {/* Pracownicy */}
        <div className="w-full">
          <button
            type="button"
            onClick={() => toggle('employees')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'employees' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'employees'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faUsers} className="h-4 w-4" />
              <span>Pracownicy</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'employees' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'employees' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'employees'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/moderator/users"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/users') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Lista pracowników
              </Link>
              <Link
                href="/moderator/users/create"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/users/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faPlus} className="mr-2" /> Dodaj pracownika
              </Link>
            </div>
          </div>
        </div>
        {/* Departaments */}
        <div className="w-full mt-3">
          <button
            type="button"
            onClick={() => toggle('departaments')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'departaments' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'departaments'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faSitemap} className="h-4 w-4" />
              <span>Wydziały</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'departaments' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'departaments' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'departaments'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/moderator/departments"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/departaments') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Lista Wydziały
              </Link>
              <Link
                href="/moderator/departaments/active-employees"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/departaments/active-employees') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faUsers} className="mr-2" /> Lista pracowników na wydziałach
              </Link>
              <Link
                href="/moderator/machines/create"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/departaments/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faPlus} className="mr-2" /> Dodaj wydział
              </Link>
            </div>
          </div>
        </div>
        {/* Maszyny */}
        <div className="w-full mt-3">
          <button
            type="button"
            onClick={() => toggle('machines')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'machines' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'machines'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faCogs} className="h-4 w-4" />
              <span>Maszyny</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'machines' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'machines' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'machines'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/moderator/machines"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/machines') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Lista maszyn
              </Link>
              <Link
                href="/moderator/machines/add-new"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/machines/add-new') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faPlus} className="mr-2" /> Dodaj maszynę
              </Link>
              <Link
                href="/moderator/machines/operations"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/machines/operations') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faWrench} className="mr-2" /> Operacje
              </Link>
            </div>
          </div>
        </div>
               {/* Awarie */}
        <div className="w-full mt-3">
          <button
            type="button"
            onClick={() => toggle('incidents')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'incidents' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'incidents'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faExclamationTriangle} className="h-4 w-4" />
              <span>Awarie</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'incidents' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'incidents' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'incidents'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/machines/report-failure"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/incidents') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Lista awarii
              </Link>
              <Link
                href="/moderator/incidents/create"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/incidents/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faPlus} className="mr-2" /> Zgłoś awarię
              </Link>
              <Link
                href="/moderator/incidents/history"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/incidents/history') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Historia
              </Link>
              <Link
                href="/moderator/incidents/reports"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/incidents/reports') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faChartLine} className="mr-2" /> Raporty
              </Link>
            </div>
          </div>
        </div>
        {/* Wydajność */}
        <div className="w-full mt-3">
          <button
            type="button"
            onClick={() => toggle('performance')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'performance' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'performance'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faChartLine} className="h-4 w-4" />
              <span>Wydajność</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'performance' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'performance' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'performance'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/moderator/performance"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/performance') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Przegląd wydajności
              </Link>
              <Link
                href="/moderator/performance/reports"
                className={`text-sm px-2 py-1 rounded block ${isActive('/moderator/performance/reports') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faPlus} className="mr-2" /> Raporty
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
