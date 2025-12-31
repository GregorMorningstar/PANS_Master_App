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
  faGraduationCap,
  faBriefcase,
  faCalendar,
  faCogs,
  faList,
  faWrench,
  faUser,
} from '@fortawesome/free-solid-svg-icons';

export default function EmployeeSidebarMenu(): React.ReactElement {
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
      href: '/employee/dashboard',
      icon: FaTachometerIcon,
    },
  ];

  const [openKey, setOpenKey] = useState<'education' | 'company' | 'calendar' | 'machines' | 'profile' | null>(null);
  const toggle = (key: 'education' | 'company' | 'calendar' | 'machines' | 'profile') =>
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
           {/* Kalendarz */}
        <div className="w-full mt-3">
          <button
            type="button"
            onClick={() => toggle('calendar')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'calendar' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'calendar'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faCalendar} className="h-4 w-4" />
              <span>Kalendarz</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'calendar' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'calendar' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'calendar'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/employee/calendar"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/calendar') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Mój grafik
              </Link>
              <Link
                href="/employee/calendar/history"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/calendar/history') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Nieobecności
              </Link>
            </div>
          </div>
        </div>

        {/* Mój Profil */}
        <div className="w-full mt-3">
          <button
            type="button"
            onClick={() => toggle('profile')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'profile' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'profile'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faUser} className="h-4 w-4" />
              <span>Mój Profil</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'profile' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'profile' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'profile'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/employee/profile"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/profile') && !isActive('/employee/address') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faUser} className="mr-2" /> Mój profil
              </Link>
              <Link
                href="/employee/adress"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/adress') && !isActive('/employee/adress/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Mój adres
              </Link>
              <Link
                href="/employee/adress/create"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/adress/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Dodaj adres
              </Link>
            </div>
          </div>
        </div>

        {/* Edukacja */}
        <div className="w-full">
          <button
            type="button"
            onClick={() => toggle('education')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'education' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'education'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faGraduationCap} className="h-4 w-4" />
              <span>Edukacja</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'education' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'education' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'education'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/employee/education"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/education') && !isActive('/employee/education/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Moja edukacja
              </Link>
              <Link
                href="/employee/education/create"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/education/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Dodaj edukację
              </Link>
            </div>
          </div>
        </div>

        {/* Firma */}
        <div className="w-full mt-3">
          <button
            type="button"
            onClick={() => toggle('company')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-md transition
              ${openKey === 'company' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-100 dark:hover:bg-gray-800'}`}
            aria-expanded={openKey === 'company'}
          >
            <div className="flex items-center gap-2 text-sm font-medium">
              <FontAwesomeIcon icon={faBriefcase} className="h-4 w-4" />
              <span>Firma</span>
            </div>
            <ChevronDown
              className={`h-4 w-4 transition-transform duration-200 ${openKey === 'company' ? 'rotate-180' : 'rotate-0'}`}
            />
          </button>

          <div
            className={`overflow-hidden transition-all duration-300 ${openKey === 'company' ? 'max-h-64 opacity-100 mt-2' : 'max-h-0 opacity-0'}`}
            aria-hidden={openKey !== 'company'}
          >
            <div className="flex flex-col space-y-1 pl-6">
              <Link
                href="/employee/company"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/company') && !isActive('/employee/company/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Moja firma
              </Link>
              <Link
                href="/employee/company/create"
                className={`text-sm px-2 py-1 rounded block ${isActive('/employee/company/create') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Dodaj firmę
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
                href="/machines/report-failure"
                className={`text-sm px-2 py-1 rounded block ${isActive('/machines/report-failure') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faWrench} className="mr-2" /> Zgłoś uster
              </Link>
              <Link
                href="/machines/failures/history"
                className={`text-sm px-2 py-1 rounded block ${isActive('/machines/failures/history') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100'}`}
              >
                <FontAwesomeIcon icon={faList} className="mr-2" /> Historia uster
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
