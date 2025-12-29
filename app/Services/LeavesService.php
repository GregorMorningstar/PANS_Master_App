<?php

namespace App\Services;

use App\Services\Contracts\LeavesServiceInterface;
use App\Repositories\Contracts\LeavesRepositoryInterface;

class LeavesService implements LeavesServiceInterface
{
    public function __construct(private readonly LeavesRepositoryInterface $leavesRepository)
    {
    }

    public function getAllLeavesByUserId(int $userId)
    {
        return $this->leavesRepository->getAllLeavesByUserId($userId);
    }
    public function store(array $data)
    {
        return $this->leavesRepository->store($data);
    }
    public function getLeavesById(int $id): ? \App\Models\Leaves
    {
        return $this->leavesRepository->getLeavesById($id);
    }
}
