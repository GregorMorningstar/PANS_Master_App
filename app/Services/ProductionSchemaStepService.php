<?php

namespace App\Services;

use App\Services\Contracts\ProductionSchemaStepServiceInterface;
use App\Repositories\Contracts\ProductionSchemaStepRepositoryInterface;

class ProductionSchemaStepService implements ProductionSchemaStepServiceInterface
{
    public function __construct(private readonly ProductionSchemaStepRepositoryInterface $repository)
    {
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function findBySchema(int $schemaId)
    {
        return $this->repository->findBySchema($schemaId);
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
