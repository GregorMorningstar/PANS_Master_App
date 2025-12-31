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
    public function createAddress()
    {
        return Inertia::render('employee/address/create');
    }
    public function storeAddress(Request $request)
    {
        dd('store address', $request->all());
    }

    public function showCompany()
    {
        return Inertia::render('employee/company/show');
    }

    public function createCompany()
    {
        return Inertia::render('employee/company/create');
    }

    public function editCompany()
    {
        return Inertia::render('employee/company/edit');
    }

    public function storeCompany(Request $request)
    {
        // Pokaż wszystkie dane przesyłane z formularza React/Inertia
        dd([
            'method' => $request->method(),
            'all_data' => $request->all(),
            'validation_test' => 'Data received successfully from React/Inertia form'
        ]);

        $validatedData = $request->validate([
            'nip' => 'required|string|size:10',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'street' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
        ]);

        // Tutaj możesz dodać logikę zapisywania do bazy danych
        // np. Company::create($validatedData);

        // Tymczasowo wyświetlamy dane
        return redirect()->route('employee.company.show')->with('success', 'Firma została pomyślnie dodana!');
    }

    public function updateCompany(Request $request)
    {
        dd('update company', $request->all());
    }
}
