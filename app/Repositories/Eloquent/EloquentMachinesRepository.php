<?php

namespace App\Repositories\Eloquent;

use App\Models\Machines;
use App\Repositories\Contracts\MachinesRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentMachinesRepository implements MachinesRepositoryInterface
{
    protected Machines $model;

    public function __construct(Machines $model)
    {
        $this->model = $model;
    }

    public function findById(int $id): ?Machines
    {
        return $this->model->find($id);
    }

    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data): Machines
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Machines
    {
        $machine = $this->model->find($id);
        if (!$machine) {
            return null;
        }
        $machine->fill($data);
        $machine->save();
        return $machine;
    }

    public function delete(int $id): bool
    {
        $machine = $this->model->find($id);
        if (!$machine) {
            return false;
        }
        return (bool) $machine->delete();
    }

    public function hasUserAssignedMachines(int $userId): bool
    {
        return $this->model->where('user_id', $userId)->exists();
    }

    public function getAllmachinesWithOperators(): LengthAwarePaginator
    {
        return $this->model->with('operator')->paginate(15);
    }
}
