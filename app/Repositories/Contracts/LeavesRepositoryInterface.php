<?php

namespace App\Repositories\Contracts;
use App\Models\Leaves;
interface LeavesRepositoryInterface
{
    public function getAllLeavesByUserId(int $userId);
    public function store(array $data);
    public function getLeavesById(int $id): ? Leaves ;
    public function updateLeave(int $id, array $data);
    public function deleteLeave(int $id): bool;
}
