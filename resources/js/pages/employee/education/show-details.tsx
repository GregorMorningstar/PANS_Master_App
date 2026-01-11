import EducationDetailsCard from "@/components/card/education-card-detals";
import EmployeeLayout from "@/layouts/EmployeeLayout";
import { usePage, Link } from "@inertiajs/react";

export default function EducationDetails() {

    const breadcrumbs = [
        { label: 'Pracownicy', href: '/employee' },
        { label: 'Szczegóły Wykształcenia', href: '#' },
    ];

    const page = usePage();
    const { education, pagination } = page.props as {
        education?: {
            id: number;
            user_profile_id?: number;
            school_name: string;
            education_level: string;
            field_of_study?: string | null;
            start_year: number;
            end_year?: number | null;
            certificate_path?: string | null;
        };
        pagination?: {
            prev_url?: string | null;
            next_url?: string | null;
            current: number;
            total: number;
        };
    };

    if (!education) {
        return (
          <EmployeeLayout breadcrumbs={breadcrumbs} title="Szczegóły Wykształcenia">
            <div className="p-4">
              <h1 className="text-2xl font-bold mb-4">Szczegóły Wykształcenia</h1>
              <div className="text-sm text-gray-600">Brak danych o wykształceniu.</div>
            </div>
          </EmployeeLayout>
        );
    }

    return (
      <EmployeeLayout breadcrumbs={breadcrumbs} title="Szczegóły Wykształcenia" >
        <div className="p-4">
          <h1 className="text-2xl font-bold mb-4">Szczegóły Wykształcenia</h1>

          <div className="space-y-2">

            {education.certificate_path && (
              <EducationDetailsCard education={education} />
            )}
          </div>

          {/* Pagination and Navigation */}
          <div className="mt-8 border-t border-gray-200 pt-6">
            {pagination && (
              <div className="flex items-center justify-between mb-4">
                <div className="flex-1 flex justify-between sm:hidden">
                  {pagination.prev_url && (
                    <Link
                      href={pagination.prev_url}
                      className="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                      Poprzedni
                    </Link>
                  )}
                  {pagination.next_url && (
                    <Link
                      href={pagination.next_url}
                      className="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                      Następny
                    </Link>
                  )}
                </div>
                <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <p className="text-sm text-gray-700">
                      Pozycja <span className="font-medium">{pagination.current}</span> z{' '}
                      <span className="font-medium">{pagination.total}</span>
                    </p>
                  </div>
                  <div>
                    <nav className="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                      {pagination.prev_url && (
                        <Link
                          href={pagination.prev_url}
                          className="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-offset-0"
                        >
                          <span className="sr-only">Poprzedni</span>
                          <svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fillRule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clipRule="evenodd" />
                          </svg>
                        </Link>
                      )}
                      <span className="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300">
                        {pagination.current} z {pagination.total}
                      </span>
                      {pagination.next_url && (
                        <Link
                          href={pagination.next_url}
                          className="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-offset-0"
                        >
                          <span className="sr-only">Następny</span>
                          <svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fillRule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clipRule="evenodd" />
                          </svg>
                        </Link>
                      )}
                    </nav>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </EmployeeLayout>
    );
}
