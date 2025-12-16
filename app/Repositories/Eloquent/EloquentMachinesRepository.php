<?php

namespace App\Repositories\Eloquent;


use App\Models\Machines;
use App\Repositories\Contracts\MachinesRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class EloquentMachinesRepository implements MachinesRepositoryInterface
{
    public function findById(int $id): ?Machines
    {
        return Machines::find($id);
    }
    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return Machines::paginate($perPage);
    }
    public function create(array $data): Machines
    {
        return Machines::create($data);
    }
    public function update(int $id, array $data): ?Machines
    {
        $machine = Machines::find($id);
        if (!$machine) {
            return null;
        }
        $machine->fill($data);
        $machine->save();
        return $machine;
    }
    public function delete(int $id): bool
    {
        $machine = Machines::find($id);
        if (!$machine) {
            return false;
        }
        return (bool) $machine->delete();
    }

    public function hasUserAssignedMachines(int $userId): bool
    {
        return Machines::where('user_id', $userId)->exists();
    }
}
