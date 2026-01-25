<?php

namespace App\Services\Contracts;

use App\Models\SchoolCertificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EducationServiceInterface
{
public function createSchoolCertificate(User $user, array $data): SchoolCertificate;
public function getSchoolCertificateById(int $id): ?SchoolCertificate;
public function getAllPendingCertificates(array $filters = []): LengthAwarePaginator;
}
