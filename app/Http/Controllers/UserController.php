<?php

namespace App\Http\Controllers;

use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserServiceInterface $users) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 6);
        $filters = [
            'name' => $request->query('name'),
            'department' => $request->query('department'),
            'barcode' => $request->query('barcode'),
        ];

        $users = $this->users->getAllByRole($perPage, 'employee', $filters);

        return inertia('moderator/user/index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function show(int $id)
    {
        $user = $this->users->getById($id);
        return inertia('moderator/user/show', ['user' => $user]);
    }
}
