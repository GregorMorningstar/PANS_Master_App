import EmployeeLayout from "@/layouts/EmployeeLayout";
import { Head, usePage } from '@inertiajs/react';

export default function EmployeeDashboard() {




    const breadcrumbs = [
        { label: 'Panel Pracownika', href: '/employee/dashboard' },
    ];

  return (
    <>
      <Head title="Panel pracownika" />
      <EmployeeLayout breadcrumbs={breadcrumbs} title="Panel pracownika">
        <div className="p-6 bg-white rounded-lg shadow">
          <h1 className="text-2xl font-bold mb-4">Panel Pracownika</h1>
          <p className="text-gray-600">Witaj w panelu pracownika. Tutaj możesz zarządzać swoimi urlopami i harmonogramem pracy.</p>
        </div>
      </EmployeeLayout>
    </>
  );
}
