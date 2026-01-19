<?php

namespace App\Repositories\Eloquent;

use App\Models\EmploymentCertificate;
use App\Repositories\Contracts\EmploymentCertificateRepositoryInterface;

class EloquentEmploymentCertificateRepository implements EmploymentCertificateRepositoryInterface
{
    public function __construct(private readonly EmploymentCertificate $employmentCertificate)
    {
    }

    public function employeeCreateCertificate(array $data): ?EmploymentCertificate
    {
        return $this->employmentCertificate->create($data);

    }

    public function getCertificatesByUserId(int $userId): ?\Illuminate\Database\Eloquent\Collection
    {
        return $this->employmentCertificate->where('user_id', $userId)->with('user')->get();
    }

}
