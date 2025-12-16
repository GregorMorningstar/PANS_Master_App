<?php

namespace App\Services;

use App\Services\Contracts\MachinesServiceInterface;
use App\Repositories\Contracts\MachinesRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Machines;
use InvalidArgumentException;

class MachineService implements MachinesServiceInterface
{
    protected MachinesRepositoryInterface $machinesRepository;

    public function __construct(MachinesRepositoryInterface $machinesRepository)
    {
        $this->machinesRepository = $machinesRepository;
    }

    public function getAllMachines(int $perPage): LengthAwarePaginator
    {
        return $this->machinesRepository->findAll($perPage);
    }

    public function getMachineById(int $id): Machines
    {
        $machine = $this->machinesRepository->findById($id);
        if (!$machine) {
            throw new \Exception("Nie znaleziono maszyny");
        }
        return $machine;
    }

    public function createMachine(array $data): Machines
    {
        return $this->machinesRepository->create($data);
    }

    public function updateMachine(int $id, array $data): Machines
    {
        $machine = $this->machinesRepository->update($id, $data);
        if (!$machine) {
            throw new \Exception("Nie znaleziono maszyny do aktualizacji");
        }
        return $machine;
    }

    public function deleteMachine(int $id): bool
    {

        //metoda sprawdzająca czy maszyna ma przypisanych użytkowników
        if ($this->machinesRepository->hasAssignedUsers($id)) {
            throw new InvalidArgumentException('Maszyna ma przypisanych użytkowników. Najpierw odłącz użytkowników.');
        }

        return $this->machinesRepository->delete($id);
    }
}
