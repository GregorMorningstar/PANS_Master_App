<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DepartmentsRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Department;

class EloquentDepartmentsRepository implements DepartmentsRepositoryInterface
{
       public function getAllWithUsersPaginated(int $perPage): LengthAwarePaginator
       {
           return Department::with('users')->paginate($perPage);
       }
         public function create(array $data): Department
         {

              return Department::create($data);
         }

        public function existsByName(string $name): bool
        {
          return Department::where('name', $name)->exists();
        }

        public function findById(int $id): ?Department
        {
          return Department::find($id);
        }

        public function update(int $id, array $data): Department
        {
          $department = Department::findOrFail($id);
          $department->fill($data);
          $department->save();
          return $department;
        }

        public function delete(int $id): bool
        {
          $department = Department::find($id);
          if (!$department) return false;
          return (bool) $department->delete();
        }
}
