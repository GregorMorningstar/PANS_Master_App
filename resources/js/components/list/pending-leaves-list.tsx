import React, { useEffect, useMemo, useState } from "react";
import axios from "axios";

type Leave = {
    id: number;
    user_id: number;
    user?: { id: number; name: string } | null;
    start_date: string;
    end_date: string;
    status: string;
    reason?: string | null;
};

export default function PendingLeavesTable({ pendingLeaves = [] }: { pendingLeaves: Leave[] }) {
    const [items, setItems] = useState<Leave[]>(pendingLeaves);
    const [page, setPage] = useState<number>(1);
    const [perPage, setPerPage] = useState<number>(10);

    useEffect(() => setItems(pendingLeaves), [pendingLeaves]);

    const total = items.length;
    const pages = Math.max(1, Math.ceil(total / perPage));
    useEffect(() => { if (page > pages) setPage(pages); }, [pages, page]);

    const paginated = useMemo(() => {
        const start = (page - 1) * perPage;
        return items.slice(start, start + perPage);
    }, [items, page, perPage]);

    const formatDate = (d?: string) => d ? new Date(d).toLocaleDateString() : "-";

    const stats = {
        totalPending: total,
        uniqueUsers: new Set(items.map(i => i.user_id)).size,
        shown: paginated.length,
    };

    async function approve(id: number) {
        if (!confirm("Zatwierdzić urlop?")) return;
        try {
            await axios.post(`/moderator/user/leaves/${id}/store`);
            setItems(prev => prev.filter(l => l.id !== id));
        } catch (e) {
            console.error(e);
            alert("Błąd podczas zatwierdzania.");
        }
    }

    async function cancelLeave(id: number) {
        if (!confirm("Odrzucić urlop?")) return;
        try {
            await axios.post(`/moderator/user/leaves/${id}/cancel`);
            setItems(prev => prev.filter(l => l.id !== id));
        } catch (e) {
            console.error(e);
            alert("Błąd podczas odrzucania.");
        }
    }

    return (
        <div>
            <div className="mb-4 grid grid-cols-3 gap-4">
                <div className="p-3 bg-white rounded shadow">
                    <div className="text-sm text-gray-500">Łącznie oczekujących</div>
                    <div className="text-2xl font-semibold">{stats.totalPending}</div>
                </div>
                <div className="p-3 bg-white rounded shadow">
                    <div className="text-sm text-gray-500">Unikalni użytkownicy</div>
                    <div className="text-2xl font-semibold">{stats.uniqueUsers}</div>
                </div>
                <div className="p-3 bg-white rounded shadow">
                    <div className="text-sm text-gray-500">Pokazane</div>
                    <div className="text-2xl font-semibold">{stats.shown}</div>
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="min-w-full border-collapse">
                    <thead>
                        <tr className="bg-gray-100 text-left">
                            <th className="px-4 py-2 border">ID</th>
                            <th className="px-4 py-2 border">Użytkownik</th>
                            <th className="px-4 py-2 border">Data rozpoczęcia</th>
                            <th className="px-4 py-2 border">Data zakończenia</th>
                            <th className="px-4 py-2 border">Status</th>
                            <th className="px-4 py-2 border">Powód</th>
                            <th className="px-4 py-2 border">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        {paginated.length === 0 ? (
                            <tr>
                                <td className="px-4 py-3 border text-center" colSpan={7}>Brak oczekujących urlopów</td>
                            </tr>
                        ) : (
                            paginated.map((leave) => (
                                <tr key={leave.id} className="odd:bg-white even:bg-gray-50">
                                    <td className="px-4 py-2 border">{leave.id}</td>
                                    <td className="px-4 py-2 border">{leave.user?.name ?? `#${leave.user_id}`}</td>
                                    <td className="px-4 py-2 border">{formatDate(leave.start_date)}</td>
                                    <td className="px-4 py-2 border">{formatDate(leave.end_date)}</td>
                                    <td className="px-4 py-2 border">{leave.status}</td>
                                    <td className="px-4 py-2 border">{leave.reason ?? "-"}</td>
                                    <td className="px-4 py-2 border">
                                        <div className="flex gap-2">
                                            <button
                                                onClick={() => approve(leave.id)}
                                                className="px-2 py-1 bg-green-500 text-white rounded text-sm"
                                            >
                                                Zatwierdź
                                            </button>
                                            <button
                                                onClick={() => cancelLeave(leave.id)}
                                                className="px-2 py-1 bg-red-500 text-white rounded text-sm"
                                            >
                                                Odrzuć
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            <div className="mt-4 flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <span>Strona</span>
                    <select value={perPage} onChange={e => { setPerPage(Number(e.target.value)); setPage(1); }} className="border rounded p-1">
                        <option value={10}>10</option>
                        <option value={25}>25</option>
                        <option value={50}>50</option>
                    </select>
                </div>

                <div className="flex items-center gap-2">
                    <button disabled={page <= 1} onClick={() => setPage(p => Math.max(1, p - 1))} className="px-3 py-1 border rounded disabled:opacity-50">Poprzednia</button>
                    <span>{page} / {pages}</span>
                    <button disabled={page >= pages} onClick={() => setPage(p => Math.min(pages, p + 1))} className="px-3 py-1 border rounded disabled:opacity-50">Następna</button>
                </div>
            </div>
        </div>
    );
}
