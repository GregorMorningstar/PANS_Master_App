import ModeratorLayout from "@/layouts/ModeratorLayout";

export default function ConfirmationEducation() {


        const breadcrumbs = [
            { label: 'Moderator', href: '/moderator' },
            { label: 'Użytkownicy', href: '/moderator/users' },
            { label: 'Potwierdzanie Edukacji', href: '/moderator/users/confirmation-education' },
        ];

    return (

        <ModeratorLayout breadcrumbs={breadcrumbs}>

 <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">Potwierdzanie Edukacji Użytkowników</h1>
            <p>Strona do potwierdzania edukacji użytkowników przez moderatorów.</p>
        </div>
        </ModeratorLayout>
    );
}


