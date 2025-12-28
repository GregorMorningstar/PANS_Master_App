<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function getById(int $id): ?User;
    public function getAll(int $perPage = 15): LengthAwarePaginator;
    public function getAllByRole(int $perPage = 15, ?string $role = null, array $filters = []): LengthAwarePaginator;
    public function getEmployeeDetailsWithRelations(int $employeeId): ?User;

}
