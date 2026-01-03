import PendingLeavesTable from "@/components/list/pending-leaves-list";
import ModeratorLayout from "@/layouts/ModeratorLayout";
import { usePage } from "@inertiajs/react";

export default function PendingLeaves() {

const { pendingLeaves = [] } = usePage().props as {
    pendingLeaves: Array<{
        id: number;
        user_id: number;
        user?: { id: number; name: string } | null;
        start_date: string;
        end_date: string;
        status: string;
        reason?: string | null;
    }>;
};


const breadcrumbs = [
  { label: 'Moderator', url: '/moderator' },
  { label: 'Użytkownicy', url: '/moderator/users' },
  { label: 'Urlopy', url: '/moderator/user/leaves' },
  { label: 'Oczekujące', url: '/moderator/user/leaves/pending' },
];
    return (
      <ModeratorLayout breadcrumbs={breadcrumbs}>

        <PendingLeavesTable  pendingLeaves={pendingLeaves} />
      </ModeratorLayout>
    );
}
