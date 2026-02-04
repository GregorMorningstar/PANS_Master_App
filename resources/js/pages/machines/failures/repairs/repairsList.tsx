import ModeratorLayout from "@/layouts/ModeratorLayout";
import { usePage } from '@inertiajs/react';

export default function MachineFailuresRepariedList() {

    const broadcrumbsModerator = [
        { label: 'Moderator', href: '/moderator'},
        { label: 'Maszyny', href: '/moderator/machines' },
        { label: 'Lista Naprawionych Awarii', href: '/machines/failures/repairs/list' }
    ];

    const page = usePage();
    const props = page.props as any;
    const machineFailureId = props.machine_failure_id ?? null;

    return (
        <ModeratorLayout breadcrumbs={broadcrumbsModerator} title="Lista Naprawionych Awarii">
            <div className="p-6">
                <h2 className="text-lg font-semibold mb-4">Lista Naprawionych Awarii</h2>
                {machineFailureId ? (
                    <div>Wyświetlamy naprawy dla zgłoszenia id: <strong>{machineFailureId}</strong></div>
                ) : (
                    <div>Brak filtrów — wyświetl wszystkie naprawy.</div>
                )}
            </div>
        </ModeratorLayout>
    );
}
