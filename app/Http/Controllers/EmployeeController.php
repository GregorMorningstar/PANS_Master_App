<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\UserServiceInterface;
use Inertia\Inertia;

class EmployeeController extends Controller
{


    public function __construct(private readonly UserServiceInterface $userService)
    {}

    public function index()
    {
        return Inertia::render('employee/dashboard/index');
    }

    public function showDetails(int $employeeId)
    {
        $employee = $this->userService->getEmployeeDetailsWithRelations($employeeId);

        return Inertia::render('employee/share/employee-details', [
            'employee' => $employee,
        ]);
    }

    public function showEmployeeProfile()
    {
               return Inertia::render('employee/profile/show');
    }

    public function showAddress()
    {
        return Inertia::render('employee/address/show');
    }
    public function editAddress()
    {
        return Inertia::render('employee/address/edit');
    }
}
