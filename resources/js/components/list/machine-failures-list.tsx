import { GlassesIcon, Trash2Icon, PenIcon } from 'lucide-react';
import { useState, useMemo, useEffect, useCallback } from 'react';
import { useForm, router } from '@inertiajs/react';
import Barcode from 'react-barcode';

const PAGE_SIZE = 10;

interface MachineFailure {
    id: number;
    machine?: { id: number; name: string; barcode?: string; status?: string; last_failure_date?: string };
    barcode?: string;
    failure_description: string;
    reported_at: string;
    failure_rank?: number;
    user_id?: number;
    user?: { name?: string };
}

interface Props {
    allmachineFailures: MachineFailure[];
    auth: { user?: { id?: number; role?: string; name?: string } };
}

const truncateWords = (text: string | undefined, words = 50): string => {
    if (!text) return '';
    const parts = text.split(/\s+/);
    if (parts.length <= words) return text;
    return parts.slice(0, words).join(' ') + '...';
};

const mapStatus = (status: string | undefined): string => {
    if (!status) return '-';
    const map: Record<string, string> = {
        breakdown: 'Awaria',
        operational: 'Sprawna',
        maintenance: 'W serwisie',
        idle: 'Nieaktywna'
    };
    return map[status.toLowerCase()] ?? status.charAt(0).toUpperCase() + status.slice(1);
};

export default function MachineFailuresList({ allmachineFailures = [], auth = {} }: Props) {
    const user = auth?.user ?? {};
    const userRole = String(user?.role ?? '').toLowerCase();

    const [hovered, setHovered] = useState<number | null>(null);
    const [selected, setSelected] = useState<MachineFailure | null>(null);
    const [deletingId, setDeletingId] = useState<number | null>(null);
    const form = useForm();
    const [localFailures, setLocalFailures] = useState<MachineFailure[]>(allmachineFailures);

    useEffect(() => {
        setLocalFailures(allmachineFailures);
    }, [allmachineFailures]);

    // sorting state (null = no sort)
    const [sortBy, setSortBy] = useState<string | null>(null);
    const [sortDir, setSortDir] = useState<'asc' | 'desc'>('desc');

    const toggleSort = (key: string) => {
        if (sortBy === key) {
            setSortDir((d) => (d === 'asc' ? 'desc' : 'asc'));
        } else {
            setSortBy(key);
            setSortDir('desc');
        }
    };

    const handleEdit = useCallback((id: number, item?: MachineFailure) => {
        router.visit(`/machines/failures/edit/${id}`, { data: item ?? {} });
    }, []);

    const handleDelete = useCallback(async (id: number) => {
        if (!confirm('Czy na pewno chcesz usunąć to zgłoszenie awarii?')) return;
        setDeletingId(id);
        form.delete(`/machines/failures/${id}`, {
            preserveState: true,
            onSuccess: () => {
                // remove from local list so UI updates immediately
                setLocalFailures((prev) => prev.filter((f) => f.id !== id));
                if (selected?.id === id) setSelected(null);
            },
            onFinish: () => setDeletingId(null),
            onError: () => {
                // optionally show error toast (not implemented here)
            },
        });
    }, [form, selected]);

    // filters & pagination
    const [filterName, setFilterName] = useState('');
    const [filterBarcode, setFilterBarcode] = useState('');
    const [dateFrom, setDateFrom] = useState<string | null>(null);
    const [dateTo, setDateTo] = useState<string | null>(null);
    const [currentPage, setCurrentPage] = useState(1);

    // Filtered list (client-side, live)
    const filtered = useMemo(() => {
        return localFailures.filter((item) => {
            // machine name
            if (filterName && !item.machine?.name?.toLowerCase().includes(filterName.toLowerCase())) {
                return false;
            }

            // barcode
            if (filterBarcode && !item.barcode?.toLowerCase().includes(filterBarcode.toLowerCase())) {
                return false;
            }

            // date from
            if (dateFrom && item.reported_at) {
                const itemDate = new Date(item.reported_at);
                const from = new Date(dateFrom);
                from.setHours(0, 0, 0, 0);
                if (itemDate < from) return false;
            }

            // date to
            if (dateTo && item.reported_at) {
                const itemDate = new Date(item.reported_at);
                const to = new Date(dateTo);
                to.setHours(23, 59, 59, 999);
                if (itemDate > to) return false;
            }

            return true;
        });
    }, [localFailures, filterName, filterBarcode, dateFrom, dateTo]);

    // Reset page on filter change
    useEffect(() => {
        setCurrentPage(1);
    }, [filtered]);

    // Apply sorting on filtered results
    const sorted = useMemo(() => {
        const arr = [...filtered];
        if (!sortBy) return arr;
        arr.sort((a: any, b: any) => {
            const av = (sortBy === 'failure_rank' ? (a.failure_rank ?? 0) : (a[sortBy] ?? '')) as any;
            const bv = (sortBy === 'failure_rank' ? (b.failure_rank ?? 0) : (b[sortBy] ?? '')) as any;
            if (av === bv) return 0;
            if (sortDir === 'asc') return av > bv ? 1 : -1;
            return av < bv ? 1 : -1;
        });
        return arr;
    }, [filtered, sortBy, sortDir]);

    const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
    const displayed = sorted.slice((currentPage - 1) * PAGE_SIZE, currentPage * PAGE_SIZE);

    // Row background based on age of failure (days)
    const getRowBackground = useCallback((item: MachineFailure) => {
        if (!item.reported_at) return undefined;
        const reported = new Date(item.reported_at);
        if (isNaN(reported.getTime())) return undefined;
        const now = new Date();
        const days = (now.getTime() - reported.getTime()) / (1000 * 60 * 60 * 24);
        if (days > 3) return '#f6d7db'; // pastel burgundy
        if (days > 2) return '#ffd6d6'; // pastel red
        if (days > 1) return '#fff4b0'; // pastel yellow
        return undefined;
    }, []);

    return (
        <div className="space-y-4">
            {/* Filters */}
            <div>
                <div className="flex flex-wrap items-center gap-4">
                    <div className="flex gap-2">
                        <button className="px-3 py-1 text-sm border rounded">All</button>
                        {userRole === 'moderator' && (
                            <button className="px-3 py-1 text-sm border rounded">Moderator Action</button>
                        )}
                    </div>

                    <div className="flex flex-wrap items-center gap-2 ml-auto">
                        <input
                            placeholder="Nazwa maszyny"
                            value={filterName}
                            onChange={(e) => setFilterName(e.target.value)}
                            className="px-3 py-2 text-sm border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <input
                            placeholder="Barcode"
                            value={filterBarcode}
                            onChange={(e) => setFilterBarcode(e.target.value)}
                            className="px-3 py-2 text-sm border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <label className="flex items-center gap-1 text-xs text-gray-600">
                            Od
                            <input
                                type="date"
                                value={dateFrom ?? ''}
                                onChange={(e) => setDateFrom(e.target.value || null)}
                                className="px-2 py-1 text-xs border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </label>
                        <label className="flex items-center gap-1 text-xs text-gray-600">
                            Do
                            <input
                                type="date"
                                value={dateTo ?? ''}
                                onChange={(e) => setDateTo(e.target.value || null)}
                                className="px-2 py-1 text-xs border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </label>
                        <button
                            onClick={() => {
                                setFilterName('');
                                setFilterBarcode('');
                                setDateFrom(null);
                                setDateTo(null);
                            }}
                            className="px-4 py-2 text-sm border rounded hover:bg-gray-50"
                        >
                            Wyczyść
                        </button>
                    </div>
                </div>
            </div>

            {/* Table */}
            <div className="overflow-x-auto">
                <table className="w-full border-collapse">
                    <colgroup>
                        <col style={{ width: '20%' }} />
                        <col style={{ width: '40%' }} />
                        <col style={{ width: '6%' }} />
                        <col style={{ width: '15%' }} />
                        <col style={{ width: '10%' }} />
                        <col style={{ width: '9%' }} />
                    </colgroup>
                    <thead>
                        <tr className="bg-gray-50">
                            <th className="p-3 text-left font-medium text-gray-900">Nazwa</th>
                            <th className="p-3 text-left font-medium text-gray-900">Opis</th>
                            <th
                                className="p-3 text-left font-medium text-gray-900 cursor-pointer select-none"
                                onClick={() => toggleSort('failure_rank')}
                                title="Sortuj po rankingu"
                            >
                                Ranking {sortBy === 'failure_rank' ? (sortDir === 'asc' ? '▲' : '▼') : ''}
                            </th>
                            <th className="p-3 text-left font-medium text-gray-900">Barcode</th>
                            <th className="p-3 text-left font-medium text-gray-900">Data awarii</th>
                            <th className="p-3 text-left font-medium text-gray-900">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        {displayed.map((item) => (
                            <tr
                                key={item.id}
                                className="border-t border-gray-200 hover:bg-gray-50"
                                style={{ backgroundColor: getRowBackground(item) }}
                            >
                                <td className="p-3 font-medium">{item.machine?.name ?? '-'}</td>

                                <td className="p-3 relative max-w-xs">
                                    <div className="truncate" title={item.failure_description}>
                                        {truncateWords(item.failure_description, 15)}
                                    </div>
                                    {hovered === item.id && (
                                        <div className="absolute left-0 top-full z-20 mt-1 p-3 bg-white border border-gray-200 rounded-lg shadow-lg max-w-sm whitespace-normal max-h-48 overflow-y-auto">
                                            {item.failure_description}
                                        </div>
                                    )}
                                </td>

                                <td className="p-3 text-sm font-medium text-gray-900">{item.failure_rank ?? '-'}</td>

                                <td className="p-3">
                                    {item.barcode ? (
                                        <div className="w-32 h-8 flex items-center justify-center">
                                            <Barcode value={item.barcode} height={32} width={1.5} />
                                        </div>
                                    ) : (
                                        <span className="text-gray-400 text-sm">-</span>
                                    )}
                                </td>

                                <td className="p-3 text-sm text-gray-900">{item.reported_at ?? '-'}</td>

                                <td className="p-3">
                                    <div className="flex items-center gap-2">
                                        <button
                                            onClick={() => setSelected(item)}
                                            onMouseEnter={() => setHovered(item.id)}
                                            onMouseLeave={() => setHovered(null)}
                                            className="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Szczegóły"
                                            aria-label="Szczegóły"
                                        >
                                            <GlassesIcon className="w-4 h-4" />
                                        </button>

                                        {(userRole === 'moderator' || item.user_id === user?.id) && (
                                            <>
                                                <button
                                                    onClick={() => handleEdit(item.id, item)}
                                                    className="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                    title="Edytuj"
                                                >
                                                    <PenIcon className="w-4 h-4" />
                                                </button>
                                                <button
                                                    onClick={() => handleDelete(item.id)}
                                                    disabled={deletingId === item.id}
                                                    className="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent rounded-lg transition-colors"
                                                    title="Usuń"
                                                >
                                                    {deletingId === item.id ? (
                                                        <div className="w-4 h-4 border-2 border-gray-300 border-t-transparent rounded-full animate-spin" />
                                                    ) : (
                                                        <Trash2Icon className="w-4 h-4" />
                                                    )}
                                                </button>
                                            </>
                                        )}
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {totalPages > 1 && (
                <div className="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                    <div className="text-sm text-gray-700">
                        Pokazano {displayed.length} z {filtered.length} ({totalPages} stron)
                    </div>
                    <div className="flex items-center gap-1">
                        <button
                            onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                            disabled={currentPage === 1}
                            className="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            &laquo; Poprzednia
                        </button>

                        {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                            const pageNum = Math.max(1, Math.min(totalPages, currentPage - 2 + i));
                            return (
                                <button
                                    key={pageNum}
                                    onClick={() => setCurrentPage(pageNum)}
                                    className={`px-3 py-2 text-sm font-medium border rounded-md transition-colors ${
                                        pageNum === currentPage
                                            ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                                            : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'
                                    }`}
                                >
                                    {pageNum}
                                </button>
                            );
                        })}

                        <button
                            onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
                            disabled={currentPage === totalPages}
                            className="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Następna &raquo;
                        </button>
                    </div>
                </div>
            )}

            {/* Details Modal */}
            {selected && (
                <div
                    className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                    onClick={() => setSelected(null)}
                >
                    <div
                        className="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
                        onClick={(e) => e.stopPropagation()}
                    >
                        {/* Header */}
                        <div className="p-6 border-b border-gray-200">
                            <div className="flex items-start justify-between gap-6">
                                <div className="flex-1">
                                    <h2 className="text-2xl font-bold text-gray-900 mb-2">
                                        {selected.machine?.name ?? 'Nazwa maszyny'}
                                    </h2>
                                    <div className="space-y-1 text-sm text-gray-600">
                                        <div><strong>Stan:</strong> {mapStatus(selected.machine?.status)}</div>
                                        <div><strong>Ostatnia awaria:</strong> {selected.machine?.last_failure_date ?? '-'}</div>
                                    </div>
                                </div>
                                <div className="flex-shrink-0 text-right min-w-[140px]">
                                    {selected.machine?.barcode ? (
                                        <div className="mb-2">
                                            <Barcode value={selected.machine.barcode} height={60} />
                                        </div>
                                    ) : (
                                        <div className="text-gray-500 text-sm">Brak kodu</div>
                                    )}
                                    {selected.machine?.id && (
                                        <div className="text-xs text-gray-500">ID: {selected.machine.id}</div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Content */}
                        <div className="p-6">
                            <h3 className="text-lg font-semibold mb-4 text-gray-900">Szczegóły awarii</h3>

                            <div className="mb-6 p-4 bg-gray-50 rounded-lg">
                                <strong className="block mb-2 text-sm font-medium text-gray-900">Opis:</strong>
                                <p className="whitespace-pre-wrap text-gray-900 leading-relaxed">
                                    {selected.failure_description}
                                </p>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <strong className="text-sm font-medium text-gray-900">Ranking:</strong>
                                    <div className="mt-1 text-sm text-gray-900">{selected.failure_rank ?? '-'}</div>
                                </div>
                                <div>
                                    <strong className="text-sm font-medium text-gray-900">Zgłoszona:</strong>
                                    <div className="mt-1 text-sm text-gray-900">{selected.reported_at ?? '-'}</div>
                                </div>
                                <div>
                                    <strong className="text-sm font-medium text-gray-900">Użytkownik:</strong>
                                    <div className="mt-1 flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="1.5">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        {selected.user?.name ?? `User #${selected.user_id ?? '-'}`}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Footer */}
                        <div className="flex justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                            {(userRole === 'moderator' || selected.user_id === user?.id) && (
                                <>
                                    <button
                                        onClick={() => handleEdit(selected.id)}
                                        className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                    >
                                        Edytuj
                                    </button>
                                    <button
                                        onClick={() => handleDelete(selected.id)}
                                        disabled={deletingId === selected.id}
                                        className="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        {deletingId === selected.id ? 'Usuwanie...' : 'Usuń'}
                                    </button>
                                </>
                            )}
                            <button
                                onClick={() => setSelected(null)}
                                className="px-6 py-2 text-sm font-medium text-blue-600 bg-transparent border border-blue-600 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                            >
                                Zamknij
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
