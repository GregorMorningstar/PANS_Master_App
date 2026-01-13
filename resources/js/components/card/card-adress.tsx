import React from "react";
import { usePage } from "@inertiajs/react";

type Props = {
    address?: any | null;
};

export default function CardAdress({ address }: Props) {
    const page = usePage().props as any;
    const authUser = page.auth?.user ?? null;
    const ownerId = address?.user_id ?? address?.user?.id ?? null;
    const canManage = authUser && (authUser.id === ownerId || authUser.role === 'moderator');

    const street = address?.street ?? address?.street_address ?? address?.address ?? "";
    const city = address?.city ?? address?.town ?? "";
    const postal = address?.postal_code ?? address?.zip ?? "";
    const country = address?.country ?? "";
    const note = address?.note ?? address?.notes ?? "";

    const handleEdit = () => {
        if (!address) return;
        // dopasuj nazwę trasy do swojej aplikacji (np. employee.address.edit)
        Inertia.visit(route('employee.address.edit', address.id));
    };

    const handleDelete = () => {
        if (!address) return;
        if (!confirm('Na pewno usunąć adres?')) return;
        // dopasuj nazwę trasy do swojej aplikacji (np. employee.address.destroy)
        Inertia.delete(route('employee.address.destroy', address.id));
    };

    if (!address) {
        return (
            <div className="max-w-md mx-auto bg-white shadow-lg rounded-xl overflow-hidden h-48 flex items-center justify-center">
                <div className="text-gray-600">Brak danych adresowych.</div>
            </div>
        );
    }

    return (
        <div className="max-w-md mx-auto bg-white shadow-lg rounded-xl overflow-hidden h-auto flex flex-col">
            {/* glowny panel adresu */}
            <div className="h-[25%] bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                Adres użytkownika
            </div>

            {/* Środkowy panel: 50% */}
            <div className="flex-1 bg-gray-50 p-6 overflow-auto">
                <p className="mb-3 font-semibold text-gray-700">Szczegóły adresu</p>
                <ul className="text-gray-800 space-y-1">
                    {street ? <li><span className="font-medium">Ulica:</span> {street}</li> : null}
                    {city ? <li><span className="font-medium">Miasto:</span> {city}</li> : null}
                    {postal ? <li><span className="font-medium">Kod pocztowy:</span> {postal}</li> : null}
                    {country ? <li><span className="font-medium">Kraj:</span> {country}</li> : null}
                    {note ? <li><span className="font-medium">Notatka:</span> {note}</li> : null}
                </ul>

                {/* jeśli dane niestandardowe */}
                {Object.keys(address).length > 0 && (
                    <div className="mt-4 text-sm text-gray-500">
                        {/* debug / fallback */}
                        <pre className="whitespace-pre-wrap">{JSON.stringify(address, null, 2)}</pre>
                    </div>
                )}
            </div>

            {/* Dolny panel: 25% */}
            <div className="h-[25%] bg-green-500 flex items-center justify-between text-white p-4">
                <div>
                    {canManage ? (
                        <div className="flex space-x-2">
                            <button
                                onClick={handleEdit}
                                className="bg-white text-green-600 px-3 py-1 rounded-md font-medium hover:opacity-90"
                            >
                                Edytuj
                            </button>
                            <button
                                onClick={handleDelete}
                                className="bg-red-600 text-white px-3 py-1 rounded-md font-medium hover:opacity-90"
                            >
                                Usuń
                            </button>
                        </div>
                    ) : (
                        <div className="text-sm opacity-90">Brak akcji</div>
                    )}
                </div>
                <div className="text-sm opacity-90">{city && postal ? `${city} ${postal}` : country}</div>
            </div>
        </div>
    );
}
