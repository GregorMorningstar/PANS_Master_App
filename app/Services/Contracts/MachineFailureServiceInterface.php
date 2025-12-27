<?php


namespace App\Services\Contracts;



interface MachineFailureServiceInterface
{
public function createMachineFailure(array $data, int $machineId);
public function getAllFailuresWithMachines(): array;
public function findFailureById(int $id);
public function updateMachineFailure(int $id, array $data): bool;
public function deleteMachineFailure(int $id): bool;
public function setZeroFailureCount(int $machineId): bool;
}
