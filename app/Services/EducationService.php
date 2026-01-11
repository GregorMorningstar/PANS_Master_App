<?php

namespace App\Services;

use App\Models\SchoolCertificate;
use App\Models\User;
use App\Services\Contracts\EducationServiceInterface;
use App\Repositories\Contracts\EducationRepositoryInterface;

class EducationService implements EducationServiceInterface
{
    public function __construct(
        private readonly EducationRepositoryInterface $educationRepository
    ) {
    }

    public function createSchoolCertificate(User $user, array $data): SchoolCertificate
    {
        $payload = [
            'user_id'          => $user->id,
            'school_name'      => $data['schoolName'] ?? null,
            'school_address'   => $data['schoolAddress'] ?? null,
            'start_year'       => $data['startYear'] ?? null,
            'end_year'         => $data['endYear'] ?? null,
            'education_level'  => $data['degree'] ?? null,
            'certificate_path' => $data['certificate_path'] ?? null,
        ];

        return $this->educationRepository->create($payload);
    }

    public function getSchoolCertificateById(int $id): ?SchoolCertificate
    {
        return $this->educationRepository->findById($id);
    }

    // helper used by controller
    public function getLatestCertificateForUser(User $user): ?SchoolCertificate
    {
        $collection = $this->educationRepository->getUserCertificates($user);
        return $collection->first();
    }

    // get paginated certificates for user
    public function getPaginatedCertificatesForUser(User $user, int $perPage = 4): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->educationRepository->getUserCertificatesPaginated($user, $perPage);
    }
}
