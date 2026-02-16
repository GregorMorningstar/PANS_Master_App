<?php

namespace App\Services;

use App\Services\Contracts\ItemsFinishedGoodServiceInterface;
use App\Repositories\Contracts\ItemsFinishedGoodRepositoryInterface;

class ItemsFinishedGoodService implements ItemsFinishedGoodServiceInterface
{
    public function __construct(private readonly ItemsFinishedGoodRepositoryInterface $repository)
    {
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
