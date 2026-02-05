<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\MachineFailureRepairRepositoryInterface;
use App\Models\MachineFailureRepair;
use App\Models\MachineFailure;
use Illuminate\Support\Collection;

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
            public function getFailuresByBarcode(string $barcode): Collection
            {
                $barcode = trim((string) $barcode);
                if ($barcode === '') {
                    return collect();
                }

                return $this->machineFailure->where('barcode', $barcode)->with('machine')->get();
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
