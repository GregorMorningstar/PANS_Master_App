<?php

namespace App\Repositories\Contracts;

use App\Models\ProductionSchema;

interface ProductionSchemaRepositoryInterface
{
    public function __construct(ProductionSchema $model);

    public function find(int $id);

    public function findByItem(int $itemId);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id): bool;
}
