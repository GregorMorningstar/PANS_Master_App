<?php

namespace App\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DepartmentServiceInterface
{
    /**
     * Zwraca paginowane wydziały wraz z relacją users.
     */
    public function getAllDepartmentsWithUsersPaginated(int $perPage): LengthAwarePaginator;
    public function existsByName(string $name): bool;
    public function createDepartment(array $data): \App\Models\Department;
    public function getById(int $id): ?\App\Models\Department;
    public function updateDepartment(int $id, array $data): \App\Models\Department;
    public function deleteDepartment(int $id): bool;
}
