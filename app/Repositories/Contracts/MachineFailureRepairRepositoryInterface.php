<?php

namespace App\Repositories\Contracts;
use App\Models\MachineFailureRepair;
use App\Models\MachineFailure;

interface MachineFailureRepairRepositoryInterface
{
	public function getFailuresWithMachine(int $machineId): ?MachineFailure;
}
