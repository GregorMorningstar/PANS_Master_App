import React from 'react';
import { usePage } from '@inertiajs/react';
import EmployeeLayout from '@/layouts/EmployeeLayout';

interface PageProps {
    success?: string;
}

export default function EmployeeCompanyShow() {
    const { success } = usePage<PageProps>().props;

    const breadcrumbs = [
        { label: 'Pracownik', href: '/employee' },
        { label: 'Firmy', href: '/employee/company' }
    ];

    return (
        <EmployeeLayout breadcrumbs={breadcrumbs} title="Moje firmy">
            <div className="space-y-6">
                {success && (
                    <div className="bg-green-50 border border-green-200 rounded-md p-4">
                        <div className="flex">
                            <div className="ml-3">
                                <p className="text-sm text-green-700">
                                    {success}
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                <div className="bg-white shadow rounded-lg p-6">
                    <h1 className="text-2xl font-semibold text-gray-900">Moje firmy</h1>
                    <p className="text-gray-600 mt-2">
                        Lista firm zostanie tutaj wyświetlona po implementacji bazy danych.
                    </p>

                    <div className="mt-6">
                        <a href="/employee/company/create" className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Dodaj nową firmę
                        </a>
                    </div>
                </div>
            </div>
        </EmployeeLayout>
    );
}
