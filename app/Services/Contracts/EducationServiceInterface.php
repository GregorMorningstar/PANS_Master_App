<?php

namespace App\Services\Contracts;

use App\Models\SchoolCertificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface EducationServiceInterface
{
public function createSchoolCertificate(User $user, array $data): SchoolCertificate;
public function getSchoolCertificateById(int $id): ?SchoolCertificate;
}
