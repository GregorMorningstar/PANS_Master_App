<?php

namespace App\Services\Contracts;
use App\Models\MachineFailureRepair;
use App\Models\MachineFailure;

interface MachineFailureRepairServiceInterface
{
    public function getFailuresWithMachine(int $machineId): ?MachineFailure;
    public function create(array $data): MachineFailureRepair;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
        public function getFailureMachineWithMachine(int $machineId): ?MachineFailure;

}
