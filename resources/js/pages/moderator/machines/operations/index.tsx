import ModeratorLayout from "@/layouts/ModeratorLayout"

export default function MachineOperationModerator() {
const breadcrumbs = [
            { label: "Panel", href: "/moderator/dashboard" },
            { label: "Maszyny", href: "/moderator/machines" },
            { label: "Operacje", href: "/moderator/machines/operations" },
        ];
    return (
        <ModeratorLayout breadcrumbs={breadcrumbs} title="Machine Operations">
            lista operacji na maszynie
        </ModeratorLayout>
    );


}
