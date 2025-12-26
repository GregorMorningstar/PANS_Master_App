<?php

namespace App\Services;

use App\Services\Contracts\UserServiceInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function getById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->findAll($perPage);
    }

    public function getAllByRole(int $perPage = 10, ?string $role = null, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->findAllByRole($perPage, $role, $filters);
    }
}
