<?php

namespace App\Services\Contracts;
use App\Models\EmploymentCertificate;

interface EmploymentCertificateServiceInterface
{
	public function employeeCreateCertificate(array $data): ?EmploymentCertificate;
    public function getCertificatesByUserId(int $userId): ?\Illuminate\Database\Eloquent\Collection;
    public function getAllCertificatesWithPendingStatus(int $perPage = 6, array $filters = []) : ?\Illuminate\Contracts\Pagination\LengthAwarePaginator;
    }
