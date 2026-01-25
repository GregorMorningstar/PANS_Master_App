import React from "react";
import { router } from "@inertiajs/react";

export default function EducationUserTable({
    users,
    educationCertificates,
}: {
    users: any[];
    educationCertificates: any[];
}) {
    const findUser = (id: number) => users?.find((u: any) => u.id === id) ?? null;

    const approve = (id: number) => {
        if (!confirm("Potwierdzić świadectwo?")) return;
        router.post(`/moderator/employee/education/${id}/approve`);
    };

    const reject = (id: number) => {
        if (!confirm("Odrzucić świadectwo?")) return;
        router.post(`/moderator/employee/education/${id}/reject`);
    };

    return (
        <div className="overflow-x-auto bg-white shadow rounded-md">
            <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                    <tr>
                        <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Użytkownik</th>
                        <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Nazwa szkoły</th>
                        <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Adres</th>
                        <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Okres edukacji</th>
                        <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                        <th className="px-4 py-2 text-center text-sm font-medium text-gray-700">Akcje</th>
                        <th className="px-4 py-2 text-center text-sm font-medium text-gray-700">Podgląd</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                    {educationCertificates?.length ? educationCertificates.map((cert: any) => {
                        const relatedUser = cert.user ?? findUser(cert.user_id);
                        const displayName = relatedUser
                            ? (relatedUser.name ?? relatedUser.user?.name ?? relatedUser.email ?? '—')
                            : '—';

                        // show only year (works for YYYY, YYYY-MM-DD or numeric year)
                        const start = cert.start_year ? String(cert.start_year).slice(0, 4) : "-";
                        const end = cert.end_year ? String(cert.end_year).slice(0, 4) : "-";
                        const previewUrl = cert.certificate_file_path ? `/storage/${cert.certificate_file_path}` : null;

                        return (
                            <tr key={cert.id}>
                                <td className="px-4 py-3 text-sm text-gray-700">{displayName}</td>
                                <td className="px-4 py-3 text-sm text-gray-700">{cert.school_name ?? "—"}</td>
                                <td className="px-4 py-3 text-sm text-gray-700">{cert.school_address ?? "—"}</td>
                                <td className="px-4 py-3 text-sm text-gray-700">{start} — {end}</td>
                                <td className="px-4 py-3 text-sm">
                                    <span className={`px-2 py-1 rounded text-xs font-semibold ${
                                        cert.status === "approved" ? "bg-green-100 text-green-800" :
                                        cert.status === "rejected" ? "bg-red-100 text-red-800" :
                                        "bg-yellow-100 text-yellow-800"
                                    }`}>
                                        {cert.status ?? "pending"}
                                    </span>
                                </td>
                                <td className="px-4 py-3 text-sm text-center">
                                    <div className="inline-flex gap-2">
                                        <button
                                            onClick={() => approve(cert.id)}
                                            className="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                        >
                                            Potwierdź
                                        </button>
                                        <button
                                            onClick={() => reject(cert.id)}
                                            className="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                                        >
                                            Odrzuć
                                        </button>
                                    </div>
                                </td>
                                <td className="px-4 py-3 text-sm text-center">
                                    {previewUrl ? (
                                        <a
                                            href={previewUrl}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="text-indigo-600 hover:underline text-sm"
                                        >
                                            Podgląd
                                        </a>
                                    ) : (
                                        <span className="text-gray-400 text-sm">Brak</span>
                                    )}
                                </td>
                            </tr>
                        );
                    }) : (
                        <tr>
                            <td className="px-4 py-6 text-center text-sm text-gray-500" colSpan={7}>
                                Brak rekordów
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>
    );
}
