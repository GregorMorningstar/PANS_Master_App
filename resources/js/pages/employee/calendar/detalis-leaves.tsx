import EmployeeLayout from "@/layouts/EmployeeLayout";
import LeavesDetailsCard from "@/components/card/leaves-detalis-card";
import { usePage } from '@inertiajs/react';

export default function LeavesDetails() {
    const { props: pageProps } = usePage<any>();
    const leaveData = pageProps?.leave ?? null;
    const userLeaves = pageProps?.userLeaves ?? [];

    const breadcrumbs = [
        { label: 'Kalendarz', href: '/employee/calendar' },
        { label: 'Szczegóły urlopu', href: '/employee/calendar/details-leaves' },
    ];

    return (
        <EmployeeLayout breadcrumbs={breadcrumbs}>
            <LeavesDetailsCard
                leave={leaveData}
                userLeaves={userLeaves}
                {...pageProps}
            />
        </EmployeeLayout>
    );
}
