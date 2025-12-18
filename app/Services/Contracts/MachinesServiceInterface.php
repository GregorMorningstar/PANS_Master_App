<?php

namespace App\Services\Contracts;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
USE App\Models\Machines;
interface MachinesServiceInterface
{
public function getAllMachines(int $perPage): LengthAwarePaginator;
public function getMachineById(int $id): Machines;
public function createMachine(array $data): Machines;
public function updateMachine(int $id, array $data): Machines;
public function deleteMachine(int $id): bool;
public function getAllmachinesWithOperators(): LengthAwarePaginator;    

}
