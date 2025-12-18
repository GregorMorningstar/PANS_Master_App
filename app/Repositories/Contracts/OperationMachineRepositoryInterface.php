<?php

namespace App\Repositories\Contracts;

use App\Models\Operationmachine;

interface OperationMachineRepositoryInterface
{
    public function create(array $data): Operationmachine;
    public function getAllOperationsByMachineId(int $machineId);
    public function findById(int $id);
    public function updateById(int $id, array $data): bool;
    public function deleteById(int $id): bool;
}
