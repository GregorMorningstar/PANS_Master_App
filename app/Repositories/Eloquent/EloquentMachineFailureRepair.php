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
}
