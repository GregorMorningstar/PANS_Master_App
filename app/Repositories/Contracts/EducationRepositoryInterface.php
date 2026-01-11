<?php

namespace App\Repositories\Contracts;

use App\Models\SchoolCertificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface EducationRepositoryInterface
{
    public function findById(int $id): ?SchoolCertificate;

    public function getUserCertificates(User $user): Collection;

    public function getUserCertificatesPaginated(User $user, int $perPage = 4): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function create(array $data): SchoolCertificate;

    // return all certificates for given user id (by user -> user_profile relation)
    public function getAllByCurrentUserId(int $id): Collection;
}
