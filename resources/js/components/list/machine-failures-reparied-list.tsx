import React from 'react';
import { router } from '@inertiajs/react';

type Repair = {
    id: number;
    barcode?: string | null;
    status?: string | null;
    cost?: number | null;
    started_at?: string | null;
    finished_at?: string | null;
    repair_order_no?: string | null;
};

type Props = {
    repairs: Repair[];
};

function formatDate(d?: string | null) {
    if (!d) return '-';
    const dt = new Date(d);
    if (isNaN(dt.getTime())) return d;
    return dt.toLocaleString();
}

export default function MachineFailuresRepariedList({ repairs = [] }: Props) {
    const handleAdd = (machineFailureId?: number) => {
        // przekieruj do tworzenia nowej naprawy (dopasuj route jeśli potrzebne)
        router.get(`/machines/failures/repairs/create?machine_id=${machineFailureId ?? ''}`);
    };

    const handleEdit = (id: number) => {
        router.get(`/machines/failures/repairs/edit/${id}`);
    };

    const handleDelete = (id: number) => {
        if (!confirm('Na pewno usunąć tę naprawę?')) return;
        router.post(`/machines/failures/repairs/${id}?_method=DELETE`);
    };

    return (
        <div className="p-4">
            <h2 className="text-lg font-semibold mb-4">Lista Napraw</h2>
            <div className="overflow-x-auto border rounded">
                <table className="w-full text-left">
                    <thead className="bg-gray-100 text-sm">
                        <tr>
                            <th className="px-4 py-2">Barcode</th>
                            <th className="px-4 py-2">Status</th>
                            <th className="px-4 py-2">Koszt</th>
                            <th className="px-4 py-2">Data serwisu</th>
                            <th className="px-4 py-2">Zlecenie</th>
                            <th className="px-4 py-2">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        {repairs.length === 0 && (
                            <tr>
                                <td colSpan={6} className="px-4 py-6 text-center text-gray-500">
                                    Brak rekordów.
                                </td>
                            </tr>
                        )}
                        {repairs.map((r) => (
                            <tr key={r.id} className="border-t">
                                <td className="px-4 py-3">{r.barcode ?? '-'}</td>
                                <td className="px-4 py-3">{r.status ?? '-'}</td>
                                <td className="px-4 py-3">{r.cost != null ? r.cost + ' zł' : '-'}</td>
                                <td className="px-4 py-3">{formatDate(r.started_at ?? r.finished_at)}</td>
                                <td className="px-4 py-3">{r.repair_order_no ?? ('Zlecenie #' + r.id)}</td>
                                <td className="px-4 py-3">
                                    <div className="flex gap-2">
                                        <button
                                            className="text-sm text-green-600 hover:underline"
                                            onClick={() => handleAdd(r.id as number)}
                                        >
                                            Dodaj kolejny
                                        </button>
                                        <button
                                            className="text-sm text-blue-600 hover:underline"
                                            onClick={() => handleEdit(r.id as number)}
                                        >
                                            Edytuj
                                        </button>
                                        <button
                                            className="text-sm text-red-600 hover:underline"
                                            onClick={() => handleDelete(r.id as number)}
                                        >
                                            Usuń
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
