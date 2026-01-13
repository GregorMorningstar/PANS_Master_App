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

}
