<?php

namespace App\Repositories\Eloquent;

use App\Models\SchoolCertificate;
use App\Models\User;
use App\Repositories\Contracts\EducationRepositoryInterface;
use App\Repositories\Contracts\FlagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\StatusAplication;

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

    public function getAllPendingCertificates(int $perPage = 6, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->schoolCertificate->where('status', 'pending')->with('user');

        if (!empty($filters['name'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['name'] . '%');
            });
        }

        if (!empty($filters['school'])) {
            $query->where('school_name', 'LIKE', '%' . $filters['school'] . '%');
        }

        if (!empty($filters['year_from'])) {
            $query->where('start_year', '>=', $filters['year_from']);
        }

        if (!empty($filters['year_to'])) {
            $query->where('end_year', '<=', $filters['year_to']);
        }

        return $query->paginate($perPage);
    }
}
