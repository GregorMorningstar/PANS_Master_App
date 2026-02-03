<?php

namespace App\Services;

use App\Services\Contracts\MachineFailureRepairServiceInterface;
use App\Repositories\Contracts\MachineFailureRepairRepositoryInterface;
use App\Models\MachineFailure;

class MachineFailureRepairService implements MachineFailureRepairServiceInterface
{
    public function __construct(private readonly MachineFailureRepairRepositoryInterface $repository)
    {
    }

    public function getFailuresWithMachine(int $machineId): ?MachineFailure
    {
        return $this->repository->getFailuresWithMachine($machineId);
    }
}
