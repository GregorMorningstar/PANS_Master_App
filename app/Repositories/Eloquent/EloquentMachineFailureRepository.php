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
        return $this->machineFailure->with('machine')->get()->toArray();
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

    public function setZeroFailureCount(int $machineId): bool
    {
        // policz awarie dla danej maszyny
        $count = $this->machineFailure->where('machine_id', $machineId)->count();
        // jeśli brak awarii (0), zmień status maszyny na 'INACTIVE'
        if ($count === 0) {
            try {
                $updated = $this->machineService->updateStatus($machineId, MachineStatus::INACTIVE->value);
                return $updated;
            } catch (\Throwable $e) {
                // ignore if table/column doesn't exist
                return false;
            }
        }

        return false;
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

     public function getFailureHistory(array $filters = [], int $perPage = 15): array
    {
        // build base query with relations
        $query = $this->machineFailure
            ->with('machine.department', 'user')
            ->whereNotNull('finished_repaired_at');

        // filter by machine barcode
        if (!empty($filters['barcode'])) {
            $barcode = trim($filters['barcode']);
            $query->whereHas('machine', function ($q) use ($barcode) {
                $q->where('barcode', 'like', "%{$barcode}%");
            });
        }

        // filter by machine name
        if (!empty($filters['machine_name'])) {
            $name = trim($filters['machine_name']);
            $query->whereHas('machine', function ($q) use ($name) {
                $q->where('name', 'like', "%{$name}%");
            });
        }

        // filter by reported_at date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('reported_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('reported_at', '<=', $filters['date_to']);
        }

        $perPage = $perPage ?: 15;

        return $query->orderBy('reported_at', 'desc')
            ->paginate($perPage)
            ->toArray();
    }
}
