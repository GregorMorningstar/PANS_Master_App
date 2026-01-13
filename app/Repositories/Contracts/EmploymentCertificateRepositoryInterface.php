<?php

namespace App\Repositories\Contracts;
use App\Models\EmploymentCertificate;

interface EmploymentCertificateRepositoryInterface
{
	public function employeeCreateCertificate(array $data): ?EmploymentCertificate;
}
