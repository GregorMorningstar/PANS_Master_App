<?php


namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\MachineFailureRepositoryInterface;
use App\Models\MachineFailure;
use App\Services\Contracts\MachinesServiceInterface;
use App\Enums\MachineStatus;

class EloquentMachineFailureRepository implements MachineFailureRepositoryInterface
{
    public function __construct(private readonly MachineFailure $machineFailure,
                                private readonly MachinesServiceInterface $machineService)
    {
    }
    public function create(array $data): MachineFailure
    {
        $failure = $this->machineFailure->create($data);
        return $failure;
    }
    public function getAllFailuresWithMachines(): array
    {
        return $this->machineFailure->with('machine')->whereNull('finished_repaired_at')->get()->toArray();
    }
    public function findById(int $id): ?MachineFailure
    {
        return $this->machineFailure->find($id);
    }
    public function update(int $id, array $data): bool
    {
        $failure = $this->machineFailure->find($id);
        if (! $failure) {
            return false;
        }
        return (bool) $failure->update($data);
    }

    /**
     * Pobierz wszystkie rekordy awarii dla podanego machine_id.
     *
     * @param int $machineId
     * @return array
     */
    public function getByMachineId(int $machineId): array
    {
        return $this->machineFailure
            ->where('machine_id', $machineId)
            ->orderBy('reported_at', 'desc')
            ->get()
            ->toArray();
    }



     public function delete(int $id): bool
    {
        $failure = $this->machineFailure->findOrFail($id);
        $machineId = $failure->machine_id;

        $deleted = (bool) $failure->delete();

        // po usunięciu sprawdź czy to był ostatni rekord dla tej maszyny
        if ($deleted && $machineId) {
            $this->setZeroFailureCount($machineId);
        }

        return $deleted;
    }

  
}
