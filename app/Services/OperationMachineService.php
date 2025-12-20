<?php

namespace App\Services;

use App\Services\Contracts\OperationMachineServiceInterface;
use App\Repositories\Contracts\OperationMachineRepositoryInterface;

class OperationMachineService implements OperationMachineServiceInterface
{
    public function __construct(
        private readonly OperationMachineRepositoryInterface $operationMachineRepository
    ) {
    }

    public function createOperation(int $machineId, int $operationId, array $data)
    {
        try {
            \Log::info('OperationMachineService::createOperation called', [
                'machineId' => $machineId,
                'data' => $data
            ]);

            $createData = [
                'machine_id'        => $machineId,
                'operation_name'    => $data['operation_name'],
                'description'       => $data['description'] ?? null,
                'duration_minutes'  => $data['duration_minutes'] ?? null,
                'changeover_time'   => $data['changeover_time'] ?? null,
                // barcode generowany automatycznie w modelu
            ];
//dd($createData);
            \Log::info('Calling repository create with data', $createData);

            $result = $this->operationMachineRepository->create($createData);

            \Log::info('Repository create result', ['result' => $result ? $result->toArray() : null]);

            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Nie udało się zapisać operacji: " . $e->getMessage());
        }
    }

    public function getAllOperationsWithMachines()
    {
        return $this->operationMachineRepository->getAll();
    }
}
