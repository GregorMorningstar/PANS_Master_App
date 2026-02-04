<?php

namespace App\Repositories\Contracts;
use App\Models\MachineFailureRepair;
use App\Models\MachineFailure;

interface MachineFailureRepairRepositoryInterface
{
	public function getFailuresWithMachine(int $machineId): ?MachineFailure;
    public function create(array $data): MachineFailureRepair;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getFailureMachineWithMachine(int $machineId): ?MachineFailure;
}
