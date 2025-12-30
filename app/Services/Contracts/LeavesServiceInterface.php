<?php


namespace App\Services\Contracts;
use App\Models\Leaves;
interface LeavesServiceInterface
{

    public function getAllLeavesByUserId(int $userId);
    public function store(array $data);
    public function getLeavesById(int $id): ? Leaves ;
    public function updateLeave(int $id, array $data);
    public function deleteLeave(int $id): bool;
}
