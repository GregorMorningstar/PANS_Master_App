<?php


namespace App\Services\Contracts;


interface OperationMachineServiceInterface
{
public function createOperation(int $machineId, int $operationId, array $data);



}
