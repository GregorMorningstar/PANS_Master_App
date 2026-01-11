<?php

namespace App\Repositories\Eloquent;

use App\Models\SchoolCertificate;
use App\Models\User;
use App\Repositories\Contracts\EducationRepositoryInterface;
use App\Repositories\Contracts\FlagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentEducationRepository implements EducationRepositoryInterface
{
    public function __construct(
        private readonly SchoolCertificate $schoolCertificate,
        private readonly FlagRepositoryInterface $flagRepository
    ) {
    }

    public function findById(int $id): ?SchoolCertificate
    {
        return $this->schoolCertificate->find($id);
    }

    public function getUserCertificates(User $user): Collection
    {
        // return certificates linked directly by user_id
        return $this->schoolCertificate
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getUserCertificatesPaginated(User $user, int $perPage = 4): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->schoolCertificate
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function create(array $data): SchoolCertificate
    {
        $education = $this->schoolCertificate->create($data);

        try {
            $this->flagRepository->educationFlagsByUser();
        } catch (\Throwable $e) {
            // ignore flag update errors
        }

        return $education;
    }

    public function update(SchoolCertificate $certificate, array $data): SchoolCertificate
    {
        $certificate->update($data);

        try {
            $this->flagRepository->educationFlagsByUser();
        } catch (\Throwable $e) {
            // ignore
        }

        return $certificate;
    }

    public function delete(SchoolCertificate $certificate): bool
    {
        return $certificate->delete();
    }

    public function getAllByCurrentUserId(int $id): Collection
    {
        // find certificates by user_id
        return $this->schoolCertificate
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->get();
    }
}
