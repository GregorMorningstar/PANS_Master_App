import ModeratorLayout from "@/layouts/ModeratorLayout";

export default function moderatorMachineIndex() {

    const breadcrumbs = [
        { label: 'Home', href: '/moderator' },
        { label: 'Panel sterowania maszyn', href: '/moderator/machines' },
    ];
    return (
        <ModeratorLayout breadcrumbs={breadcrumbs} title="Panel sterowania maszyn">
            <div>
                <h1 className="text-2xl font-bold mb-4">Machines Management</h1>
                {/* Add your machine management UI components here */}
            </div>
        </ModeratorLayout>
    )
}
