<?php

namespace App\Services\Contracts;
use App\Models\MachineFailureRepair;
use App\Models\MachineFailure;

interface MachineFailureRepairServiceInterface
{
    public function getFailuresWithMachine(int $machineId): ?MachineFailure;
}
