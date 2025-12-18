import MachineList from "@/components/list/machine-list";
import MachineCardSimple from "@/components/card/machineCardSimple";
import ModeratorLayout from "@/layouts/ModeratorLayout"
import { usePage } from "@inertiajs/react";

type PageProps = {
    allOperations?: any[] | null;
    auth?: {
        user?: any | null;
    } | null;
};

export default function MachineOperationModerator() {
    const props = usePage<PageProps>().props;
    const allOperations = props.allOperations;
    const sampleMachine = Array.isArray(allOperations) && allOperations.length > 0 ? allOperations[0] : null;

    const breadcrumbs = [
        { label: "Panel", href: "/moderator/dashboard" },
        { label: "Maszyny", href: "/moderator/machines" },
        { label: "Operacje", href: "/moderator/machines/operations" },
    ];

    return (
        <ModeratorLayout breadcrumbs={breadcrumbs} title="Machine Operations">
            <MachineList allOperations={allOperations} />
            

        </ModeratorLayout>
    );
}
