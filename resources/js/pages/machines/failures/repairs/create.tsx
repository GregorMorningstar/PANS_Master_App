import ModeratorLayout from "@/layouts/ModeratorLayout";
import { usePage, useForm } from '@inertiajs/react';
import { useState } from 'react';

type RepairFormData = {
    machine_failure_id: number | null;
    status: string;
    cost: string;
    description: string;
    started_at: string;
    finished_at: string;
};

export default function ReportFailurePageCreate() {
    const breadcrumbsModerator = [
        { label: 'Moderator', href: '/moderator' },
        { label: 'Maszyny', href: '/moderator/machines' },
        { label: 'Naprawa Awarii', href: '/machines/failures/fix' }
    ];

    const page = usePage();
    const props = page.props as any;
    const { machineFailure, statuses = {} } = props;

    const toLocalDatetime = (s?: string) => {
        if (!s) return '';
        const d = new Date(s);
        if (isNaN(d.getTime())) return '';
        const pad = (n: number) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    const form = useForm<RepairFormData>({
        machine_failure_id: machineFailure?.id ?? null,
        status: Object.keys(statuses)[0] ?? 'reported',
        cost: '',
        description: '',
        started_at: toLocalDatetime(machineFailure?.reported_at ?? ''),
        finished_at: '',
    });

    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        form.post('/machines/failures/repairs', {
            onFinish: () => setSubmitting(false),
        });
    };

    return (
        <ModeratorLayout breadcrumbs={breadcrumbsModerator} title="Naprawa Awarii">
            <div className="p-6">
                <div className="flex justify-center">
                    <form onSubmit={handleSubmit} className="w-full max-w-2xl bg-white border border-gray-200 rounded-xl shadow-md p-6">
                        <div className="flex items-start justify-between mb-4">
                            <div>
                                <h2 className="text-xl font-semibold">Formularz naprawy</h2>
                                <p className="text-sm text-gray-500">Uzupełnij dane naprawy dla wybranej awarii</p>
                            </div>
                        </div>

                        <div className="space-y-4">
                            {/* Nazwa maszyny i Barcode */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-gray-500 mb-1">Nazwa maszyny</label>
                                    <div className="px-3 py-2 border rounded bg-gray-50 text-sm font-medium">
                                        {machineFailure?.machine?.name ?? '-'}
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-xs text-gray-500 mb-1">Barcode</label>
                                    <div className="px-3 py-2 border rounded bg-gray-50 text-sm font-mono">
                                        {machineFailure?.barcode ?? '-'}
                                    </div>
                                </div>
                            </div>

                            {/* Opis awarii */}
                            <div>
                                <label className="block text-xs text-gray-500 mb-1">Opis awarii</label>
                                <div className="px-3 py-2 border rounded bg-gray-50 text-sm max-h-24 overflow-y-auto">
                                    {machineFailure?.failure_description ?? '-'}
                                </div>
                            </div>

                            {/* Status awarii i Ranking */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-xs text-gray-500 mb-1">Ranking</label>
                                    <div className="px-3 py-2 border rounded bg-gray-50 text-sm font-medium">
                                        {machineFailure?.failure_rank ?? '-'}
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-xs text-gray-500 mb-1">Zgłoszona</label>
                                    <div className="px-3 py-2 border rounded bg-gray-50 text-sm">
                                        {machineFailure?.reported_at ?? '-'}
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-xs text-gray-500 mb-1">Użytkownik</label>
                                    <div className="px-3 py-2 border rounded bg-gray-50 text-sm text-blue-600 font-medium">
                                        {machineFailure?.user?.name ?? `User #${machineFailure?.user_id ?? '-'}`}
                                    </div>
                                </div>
                            </div>

                            {/* Stan naprawy (Select) */}
                            <div>
                                <label className="block text-xs text-gray-500 mb-1">Stan naprawy *</label>
                                <select
                                    value={form.data.status}
                                    onChange={(e) => form.setData('status', e.target.value)}
                                    className="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    {Object.entries(statuses).map(([value, label]: [string, any]) => (
                                        <option key={value} value={value}>{label}</option>
                                    ))}
                                </select>
                                {form.errors.status && <div className="text-red-600 text-sm mt-1">{form.errors.status}</div>}
                            </div>

                            {/* Opis naprawy */}
                            <div>
                                <label className="block text-xs text-gray-500 mb-1">Opis naprawy *</label>
                                <textarea
                                    value={form.data.description}
                                    onChange={(e) => form.setData('description', e.target.value)}
                                    className="w-full px-3 py-2 border rounded min-h-[100px] focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Wprowadź opis wykonanej naprawy..."
                                    required
                                />
                                {form.errors.description && <div className="text-red-600 text-sm mt-1">{form.errors.description}</div>}
                            </div>

                            {/* Koszt */}
                            <div>
                                <label className="block text-xs text-gray-500 mb-1">Koszt naprawy (PLN) *</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    value={form.data.cost}
                                    onChange={(e) => form.setData('cost', e.target.value)}
                                    className="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="0.00"
                                    required
                                />
                                {form.errors.cost && <div className="text-red-600 text-sm mt-1">{form.errors.cost}</div>}
                            </div>

                            {/* Daty rozpoczęcia i zakończenia */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-gray-500 mb-1">Data rozpoczęcia</label>
                                    <input
                                        type="datetime-local"
                                        value={form.data.started_at ?? ''}
                                        className="w-full px-3 py-2 border rounded bg-gray-50 cursor-not-allowed"
                                        readOnly
                                    />
                                    {form.errors.started_at && <div className="text-red-600 text-sm mt-1">{form.errors.started_at}</div>}
                                </div>
                                <div>
                                    <label className="block text-xs text-gray-500 mb-1">Data zakończenia</label>
                                    <input
                                        type="datetime-local"
                                        value={form.data.finished_at ?? ''}
                                        onChange={(e) => form.setData('finished_at', e.target.value)}
                                        className="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                    {form.errors.finished_at && <div className="text-red-600 text-sm mt-1">{form.errors.finished_at}</div>}
                                </div>
                            </div>

                            {/* Przyciski */}
                            <div className="flex items-center justify-end gap-3 pt-4 border-t">
                                <button
                                    type="button"
                                    onClick={() => window.history.back()}
                                    className="px-4 py-2 border rounded text-sm text-gray-700 hover:bg-gray-50"
                                >
                                    Anuluj
                                </button>
                                <button
                                    type="submit"
                                    disabled={submitting || form.processing}
                                    className="px-6 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {submitting || form.processing ? 'Zapisywanie...' : 'Wyślij'}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </ModeratorLayout>
    );
}
