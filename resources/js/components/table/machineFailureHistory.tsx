import React, { useEffect, useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import Barcode from 'react-barcode';

type Failure = any;

export default function MachineFailureHistory({ history, filters }: { history?: Failure[] | any, filters?: any }) {
    const rows = history ?? [];

    const paginated = history && (history as any).data ? (history as any) : null;
    const dataRows: Failure[] = paginated ? paginated.data : (rows as Failure[]);

    const initialFilters = filters ?? {};
    const [barcode, setBarcode] = useState(initialFilters.barcode ?? '');
    const [machineName, setMachineName] = useState(initialFilters.machine_name ?? '');
    const [dateFrom, setDateFrom] = useState(initialFilters.date_from ?? '');
    const [dateTo, setDateTo] = useState(initialFilters.date_to ?? '');
    const [perPage] = useState(initialFilters.per_page ?? (paginated?.per_page ?? 15));

    const debounceRef = useRef<number | null>(null);

    const imgSrc = (path: string | null | undefined) => (path ? `/storage/${path}` : 'https://via.placeholder.com/120x90?text=Maszyna');

    const buildParams = (page?: number) => {
        const params: any = {};
        if (barcode) params.barcode = barcode;
        if (machineName) params.machine_name = machineName;
        if (dateFrom) params.date_from = dateFrom;
        if (dateTo) params.date_to = dateTo;
        if (perPage) params.per_page = perPage;
        if (page) params.page = page;
        return params;
    };

    const basePath = paginated?.path ?? window.location.pathname;

    const doSearch = (page?: number) => {
        const params = buildParams(page);
        router.get(basePath, params, { preserveState: true, replace: true, preserveScroll: true });
    };

    const scheduleSearch = (page?: number) => {
        if (debounceRef.current) window.clearTimeout(debounceRef.current);
        debounceRef.current = window.setTimeout(() => doSearch(page ?? 1), 450);
    };

    useEffect(() => {
        if (initialFilters && Object.keys(initialFilters).length > 0) {
            doSearch(paginated?.current_page ?? 1);
        }
    }, []);

    const qsFromState = (page: number) => {
        const params = buildParams(page);
        const usp = new URLSearchParams();
        Object.entries(params).forEach(([k, v]) => {
            if (v !== undefined && v !== null && String(v).length > 0) usp.append(k, String(v));
        });
        return usp.toString() ? `?${usp.toString()}` : '';
    };

    return (
        <div className="overflow-x-auto">
            <div className="mb-4 flex flex-col md:flex-row md:items-end md:justify-between gap-3">
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 flex-1">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                        <input value={barcode} onChange={(e) => { setBarcode(e.target.value); scheduleSearch(); }} placeholder="Szukaj po barcode" className="px-3 py-2 border rounded w-full" />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Nazwa maszyny</label>
                        <input value={machineName} onChange={(e) => { setMachineName(e.target.value); scheduleSearch(); }} placeholder="Szukaj po nazwie maszyny" className="px-3 py-2 border rounded w-full" />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Od</label>
                        <input type="date" value={dateFrom} onChange={(e) => { setDateFrom(e.target.value); scheduleSearch(); }} className="px-3 py-2 border rounded w-full" />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Do</label>
                        <input type="date" value={dateTo} onChange={(e) => { setDateTo(e.target.value); scheduleSearch(); }} className="px-3 py-2 border rounded w-full" />
                    </div>
                </div>
                <div className="flex items-center gap-2">
                    <button
                        type="button"
                        onClick={() => {
                            setBarcode('');
                            setMachineName('');
                            setDateFrom('');
                            setDateTo('');
                            if (debounceRef.current) window.clearTimeout(debounceRef.current);
                            doSearch(1);
                        }}
                        className="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded"
                    >
                        Wyczyść
                    </button>
                </div>
            </div>
            <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                    <tr>
                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zdjęcie</th>
                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data awarii</th>
                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data naprawy</th>
                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opis</th>
                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zgłaszający</th>
                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                    {dataRows.map((r: Failure) => {
                        const machine = r.machine ?? {};
                        const user = r.user ?? {};
                        return (
                            <tr key={r.id}>
                                <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{r.id}</td>
                                <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><Barcode value={machine.barcode ?? r.id} /></td>
                                <td className="px-4 py-3 whitespace-nowrap">
                                    <img src={imgSrc(machine.image_path)} alt={machine.name ?? 'Maszyna'} className="h-16 w-24 object-cover rounded" />
                                </td>
                                <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{r.reported_at ?? '—'}</td>
                                <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{r.finished_repaired_at ?? '—'}</td>
                                <td className="px-4 py-3 whitespace-normal text-sm text-gray-700 max-w-xs break-words">{r.failure_description ?? '—'}</td>
                                <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{user.name ?? '—'}</td>
                                <td className="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <Link href={`/machines/failures/edit/${r.id}`} className="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</Link>
                                    <Link href={`/machines/failures/edit/${r.id}`} className="text-green-600 hover:text-green-900">Edytuj</Link>
                                </td>
                            </tr>
                        );
                    })}
                </tbody>
            </table>
            {paginated && (
                <div className="mt-4 flex items-center justify-between">
                    <div className="text-sm text-gray-700">
                        Strona {paginated.current_page} z {paginated.last_page} — {paginated.total} wyników
                    </div>
                    <nav className="inline-flex -space-x-px" aria-label="Pagination">
                        {paginated.prev_page_url ? (
                            <Link href={`${paginated.path}${qsFromState(paginated.current_page - 1)}`} className="px-3 py-1 border bg-white text-sm text-gray-700">Poprzednia</Link>
                        ) : (
                            <span className="px-3 py-1 border bg-gray-100 text-sm text-gray-400">Poprzednia</span>
                        )}

                        {Array.from({ length: paginated.last_page }).map((_, i) => {
                            const page = i + 1;
                            const isCurrent = page === paginated.current_page;
                            return (
                                <Link
                                    key={page}
                                    href={`${paginated.path}${qsFromState(page)}`}
                                    className={`px-3 py-1 border text-sm ${isCurrent ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'}`}
                                >
                                    {page}
                                </Link>
                            );
                        })}

                        {paginated.next_page_url ? (
                            <Link href={`${paginated.path}${qsFromState(paginated.current_page + 1)}`} className="px-3 py-1 border bg-white text-sm text-gray-700">Następna</Link>
                        ) : (
                            <span className="px-3 py-1 border bg-gray-100 text-sm text-gray-400">Następna</span>
                        )}
                    </nav>
                </div>
            )}
        </div>
    );
}
