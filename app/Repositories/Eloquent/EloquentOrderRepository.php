<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private readonly Order $model)
    {
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->newQuery();
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['customer_name'])) {
            $query->where('customer_name', 'like', '%'.$filters['customer_name'].'%');
        }
        return $query->paginate($perPage);
    }

    public function find(int $id): ?Order
    {
        return $this->model->find($id);
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Order
    {
        $model = $this->find($id);
        if (!$model) return null;
        $model->update($data);
        return $model;
    }

    public function delete(int $id): bool
    {
        $model = $this->find($id);
        if (!$model) return false;
        return (bool) $model->delete();
    }

    public function findByBarcode(string $barcode): ?Order
    {
        return $this->model->where('barcode', $barcode)->first();
    }

    public function getItemsByOrder(int $orderId)
    {
        $order = $this->model->with('items.product')->find($orderId);
        if (!$order) return null;
        return $order->items;
    }

    public function getActiveOrdersPaginated(int $perPage = 15, array $filters = [])
    {
        $activeStatuses = [
            'accepted',
            'in_progress'
        ];

        $query = $this->model->newQuery()->whereIn('status', $activeStatuses);

        if (!empty($filters['customer_name'])) {
            $query->where('customer_name', 'like', '%'.$filters['customer_name'].'%');
        }

        return $query->paginate($perPage);
    }
}
