import React, { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";

export default function AddMachineOperationCard() {
    const [machineId, setMachineId] = useState<string>("");
    const [clientErrors, setClientErrors] = useState<Record<string, string>>({});
    const form = useForm({
        operation_name: "",
        description: "",
        duration_minutes: "",
        changeover_time: "",
    });

    useEffect(() => {
        const m = window.location.pathname.match(/\/(\d+)\/add\/?$/);
        if (m) setMachineId(m[1]);
    }, []);

    const validate = () => {
        const e: Record<string, string> = {};
        if (!form.data.operation_name || !form.data.operation_name.trim()) e.operation_name = "Nazwa operacji jest wymagana.";
        if (form.data.duration_minutes) {
            const v = Number(form.data.duration_minutes);
            if (!Number.isInteger(v) || v < 0) e.duration_minutes = "Czas trwania musi być nieujemną liczbą całkowitą.";
        }
        if (form.data.changeover_time) {
            const v = Number(form.data.changeover_time);
            if (Number.isNaN(v) || v < 0) e.changeover_time = "Czas przezbrojenia musi być liczbą >= 0.";
        }
        setClientErrors(e);
        return Object.keys(e).length === 0;
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setClientErrors({});
        if (!machineId) {
            setClientErrors({ general: "Brak ID maszyny w URL." });
            return;
        }
        if (!validate()) return;
        const postUrl = `/moderator/machines/operations/${machineId}/add`;
        form.post(postUrl, {
            onError: (errors: Record<string, any>) => {
                const mapped: Record<string, string> = {};
                for (const k in errors) mapped[k] = Array.isArray(errors[k]) ? errors[k][0] : String(errors[k]);
                setClientErrors(mapped);
            },
            onSuccess: () => form.reset(),
        });
    };

    const errorFor = (key: string) => clientErrors[key] || (form.errors as any)[key];

    return (
        <div className="p-6 bg-white border rounded shadow-sm m-4">
            <h1 className="text-2xl font-semibold mb-4">Dodaj nową operację maszyny</h1>
            {errorFor("general") && <div className="text-red-600 mb-2">{errorFor("general")}</div>}
            <form onSubmit={submit} noValidate>
                <div className="mb-4">
                    <label htmlFor="operation_name" className="block font-medium">Nazwa operacji</label>
                    <input id="operation_name" name="operation_name" type="text" value={form.data.operation_name} onChange={e => form.setData("operation_name", e.target.value)} className="mt-1 block w-full border rounded px-2 py-1" required />
                    {errorFor("operation_name") && <div className="text-red-600 text-sm mt-1">{errorFor("operation_name")}</div>}
                </div>

                <div className="mb-4">
                    <label htmlFor="description" className="block font-medium">Opis</label>
                    <textarea id="description" name="description" value={form.data.description} onChange={e => form.setData("description", e.target.value)} className="mt-1 block w-full border rounded px-2 py-1" rows={3} />
                    {errorFor("description") && <div className="text-red-600 text-sm mt-1">{errorFor("description")}</div>}
                </div>

                <div className="mb-4">
                    <label htmlFor="duration_minutes" className="block font-medium">Czas trwania (minuty)</label>
                    <input id="duration_minutes" name="duration_minutes" type="number" min={0} step={1} value={form.data.duration_minutes} onChange={e => form.setData("duration_minutes", e.target.value)} className="mt-1 block w-full border rounded px-2 py-1" />
                    {errorFor("duration_minutes") && <div className="text-red-600 text-sm mt-1">{errorFor("duration_minutes")}</div>}
                </div>

                <div className="mb-4">
                    <label htmlFor="changeover_time" className="block font-medium">Czas przezbrojenia</label>
                    <input id="changeover_time" name="changeover_time" type="number" min={0} step="0.01" value={form.data.changeover_time} onChange={e => form.setData("changeover_time", e.target.value)} className="mt-1 block w-full border rounded px-2 py-1" />
                    {errorFor("changeover_time") && <div className="text-red-600 text-sm mt-1">{errorFor("changeover_time")}</div>}
                </div>

                <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50" disabled={form.processing}>
                    {form.processing ? "Zapisywanie..." : "Zapisz"}
                </button>
            </form>
        </div>
    );
}
