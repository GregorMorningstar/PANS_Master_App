import AddMachineOperationCard from "@/components/card/add-machine-operation-card";
import ModeratorLayout from "@/layouts/ModeratorLayout";
import { usePage } from "@inertiajs/react";
import { use } from "react";

export default function CreateMachineOperation() {


    const breadcrumbs = [
            { label: "Dashboard", href: "/moderator/dashboard" },
            { label: "Machines", href: "/moderator/machines" },
            { label: "Operations", href: "/moderator/machines/operations" },
            { label: "Dodaj", href: "/moderator/machines/operations/create" },
        ];

        const page = usePage<any>();
            const machineId: number | undefined = page.props.machine_id;

    // cast to any to allow passing props until the component is typed
    const AddMachineOperationCardAny = AddMachineOperationCard as any;

    return (



        <ModeratorLayout breadcrumbs={breadcrumbs} title="Create Machine Operation">
            <AddMachineOperationCardAny initialMachineId={machineId} />
            {machineId}
        </ModeratorLayout>
    );
}
