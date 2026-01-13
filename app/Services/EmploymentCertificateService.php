<?php

namespace App\Services;

use App\Services\Contracts\EmploymentCertificateServiceInterface;
use App\Repositories\Contracts\EmploymentCertificateRepositoryInterface;
use App\Models\EmploymentCertificate;

class EmploymentCertificateService implements EmploymentCertificateServiceInterface
{
    public function __construct(private readonly EmploymentCertificateRepositoryInterface $repository)
    {
    }

    public function employeeCreateCertificate(array $data): ?EmploymentCertificate
    {
        return $this->repository->employeeCreateCertificate($data);
    }

    
}
