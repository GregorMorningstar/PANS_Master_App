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
    public function setZeroFailureCount(int $machineId): bool;
    public function getByMachineId(int $machineId): array;
    /**
     * Get paginated failure history with optional filters.
     * @param array $filters Supported keys: barcode, machine_name, date_from, date_to
     * @param int $perPage
     * @return array Paginated result (includes 'data' and pagination meta)
     */
    public function getFailureHistory(array $filters = [], int $perPage = 15): array;
}
