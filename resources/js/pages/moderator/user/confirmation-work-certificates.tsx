import ModeratorLayout from "@/layouts/ModeratorLayout";

export default function ConfirmationWorkCertificates() {
    const breadcrumbs = [
        { label: 'Moderator', href: '/moderator' },
        { label: 'Użytkownicy', href: '/moderator/users' },
        { label: 'Potwierdzanie Świadectw Pracy', href: '/moderator/users/confirmation-work-certificates' },
    ];

    return (


        <ModeratorLayout breadcrumbs={breadcrumbs} title="Potwierdzenia Świadectw Pracy">
      <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">Potwierdzanie Świadectw Pracy Użytkowników</h1>
            <p>Strona do potwierdzania świadectw pracy użytkowników przez moderatorów.</p>
        </div>
        </ModeratorLayout>

    );
}
