<?php

namespace App\Repositories\Eloquent;

use App\Models\ItemsFinishedGood;
use App\Repositories\Contracts\ItemsFinishedGoodRepositoryInterface;

class EloquentItemsFinishedGoodRepository implements ItemsFinishedGoodRepositoryInterface
{
    public function __construct(private readonly ItemsFinishedGood $model)
    {
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'] ?? null)) {
            $q = $filters['search'];
            $query->where('name', 'like', "%{$q}%");
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return null;
        }
        $item->fill($data);
        $item->save();
        return $item;
    }

    public function delete(int $id): bool
    {
        $item = $this->model->find($id);
        if (!$item) {
            return false;
        }
        return (bool) $item->delete();
    }
}
