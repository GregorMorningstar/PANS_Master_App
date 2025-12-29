<?php

namespace App\Repositories\Eloquent;

use App\Models\Leaves;
use App\Repositories\Contracts\LeavesRepositoryInterface;

class EloquentLeavesRepository implements LeavesRepositoryInterface
{


    public function __construct(private readonly Leaves $leaves)
    {
    }
    public function getAllLeavesByUserId(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->leaves->where('user_id', $userId)->with('user')->get();
    }
    public function store(array $data): Leaves
    {
        return $this->leaves->create($data);
    }
    public function getLeavesById(int $id): ?Leaves
    {
        return $this->leaves->with('user')->find($id);
    }
}


