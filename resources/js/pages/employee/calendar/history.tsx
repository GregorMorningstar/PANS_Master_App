import EmployeeLayout from "@/layouts/EmployeeLayout";

export default function EmployeeCalendarHistory() {
    const breadcrumbs = [
        {label: 'Panel pracownika', href : ('employee/dashboard') },
        { label: 'Kalendarz pracownika', href : ('employee/calendar') },
        { label: 'Historia zmian', href : ('employee/calendar/history') },
    ];
    return (
       <EmployeeLayout title="Kalendarz pracownika" breadcrumbs={breadcrumbs}>
        test calendar history
       </EmployeeLayout>
    );
}
