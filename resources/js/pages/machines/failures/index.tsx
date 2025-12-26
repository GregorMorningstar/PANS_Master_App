import ModeratorLayout from '@/layouts/ModeratorLayout';
import { usePage } from '@inertiajs/react';

export default function ReportFailurePageIndex() {
    const page = usePage();
    const rawRole = (page.props as any)?.auth?.user?.role ?? '';
    const userRole = String(rawRole).toLowerCase();
const breadcrumbsModerator = [
        { label: 'Moderator', href: '/moderator'},
        { label: 'Maszyny', href: '/moderator/machines' },
        { label: 'Lista Awarii', href: '/moderator/machines/report-failure' }
    ];

    if (userRole === 'moderator') return (
    <ModeratorLayout breadcrumbs={breadcrumbsModerator} title="Lista Awarii">
        <div>1</div>
    </ModeratorLayout>
    );
    if (userRole === 'employee') return (
        <div>2</div>
    );

    return <div />;
}
