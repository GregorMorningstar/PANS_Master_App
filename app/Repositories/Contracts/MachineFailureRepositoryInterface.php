<?php



namespace App\Repositories\Contracts;
use App\Models\MachineFailure;

interface MachineFailureRepositoryInterface
{
    public function create(array $data): MachineFailure;
    public function getAllFailuresWithMachines(): array;
    public function findById(int $id): ?MachineFailure;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
  
}
