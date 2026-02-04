<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\MachineFailureRepairRepositoryInterface;
use App\Models\MachineFailureRepair;
use App\Models\MachineFailure;

class EloquentMachineFailureRepair implements MachineFailureRepairRepositoryInterface
{
    public function __construct(private readonly MachineFailureRepair $machineFailureRepair,
    private readonly MachineFailure $machineFailure)
    {
    }

public function getFailuresWithMachine(int $machineId): ?MachineFailure
    {
        return $this->machineFailure->where('id', $machineId)->first();
    }

    public function create(array $data): MachineFailureRepair
    {
        $repair = $this->machineFailureRepair->create($data);
        return $repair;
    }

    public function update(int $id, array $data): bool
    {
        $repair = $this->machineFailureRepair->find($id);
        if (!$repair) {
            return false;
        }
        return $repair->update($data);
    }

    public function delete(int $id): bool
    {
        $repair = $this->machineFailureRepair->find($id);
        if (!$repair) {
            return false;
        }
        return $repair->delete();
    }

    public function getFailureMachineWithMachine(int $machineId): ?MachineFailure
    {
        return $this->machineFailure->where('id', $machineId)->with('machine')->first();
    }
}
