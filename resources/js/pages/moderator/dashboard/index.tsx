import ModeratorLayout from "@/layouts/ModeratorLayout";
import { Head } from '@inertiajs/react';

export default function ModeratorDashboard() {

  const breadcrumbs = [
    { label: 'Panel Moderatora', href : ('moderator/dashboard') },
  ];

  return (
    <>
      <Head title="Panel Moderatora" />
      <ModeratorLayout breadcrumbs={breadcrumbs} title="Panel Moderatora">
        test
      </ModeratorLayout>
    </>
  );
}
