<?php

namespace App\Repositories\Eloquent;

use App\Models\EmploymentCertificate;
use App\Repositories\Contracts\EmploymentCertificateRepositoryInterface;
use App\Enums\StatusAplication;


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

    public function getAllCertificatesWithPendingStatus(int $perPage = 6, array $filters = []): ?\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->employmentCertificate->where('status', StatusAplication::PENDING)->with('user');

        if (!empty($filters['name'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['name'] . '%');
            });
        }

        if (!empty($filters['nip'])) {
            $query->where('nip', 'LIKE', '%' . $filters['nip'] . '%');
        }

        if (!empty($filters['company_name'])) {
            $query->where('company_name', 'LIKE', '%' . $filters['company_name'] . '%');
        }

        if (!empty($filters['start_date_from'])) {
            $query->where('start_date', '>=', $filters['start_date_from']);
        }

        if (!empty($filters['start_date_to'])) {
            $query->where('start_date', '<=', $filters['start_date_to']);
        }

        if (!empty($filters['end_date_from'])) {
            $query->where('end_date', '>=', $filters['end_date_from']);
        }

        if (!empty($filters['end_date_to'])) {
            $query->where('end_date', '<=', $filters['end_date_to']);
        }

        return $query->paginate($perPage);
    }
}
