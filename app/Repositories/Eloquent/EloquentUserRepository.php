<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $userModel) {}

    public function findById(int $id): ?User
    {
        return $this->userModel->find($id);
    }

    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userModel->paginate($perPage);
    }

    public function findAllByRole(int $perPage = 15, ?string $role = null, array $filters = []): LengthAwarePaginator
    {
        $query = $this->userModel->newQuery()->with('department');

        if ($role) {
            $query->where('role', $role);
        }

        // Filter by name
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // Filter by department
        if (!empty($filters['department'])) {
            $query->whereHas('department', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['department'] . '%');
            });
        }

        // Filter by barcode
        if (!empty($filters['barcode'])) {
            $query->where('barcode', 'like', '%' . $filters['barcode'] . '%');
        }

        return $query->paginate($perPage);
    }
}
