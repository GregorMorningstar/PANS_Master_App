<?php

namespace App\Repositories\Contracts;
use App\Models\EmploymentCertificate;
use Illuminate\Database\Eloquent\Collection;

interface EmploymentCertificateRepositoryInterface
{
	public function employeeCreateCertificate(array $data): ?EmploymentCertificate;
    public function getCertificatesByUserId(int $userId): ?Collection;
}
