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

    public function createEducation()
    {
        return Inertia::render('employee/education/create');
    }

    public function showEducation()
    {
        return Inertia::render('employee/education/show');
    }

    public function editEducation()
    {
        return Inertia::render('employee/education/edit');
    }

    public function storeEducation(Request $request)
    {
        // Pokaż wszystkie dane przesyłane z formularza React/Inertia
        dd([
            'method' => $request->method(),
            'all_data' => $request->all(),
            'validation_test' => 'Education data received successfully from React/Inertia form'
        ]);

        $validatedData = $request->validate([
            'startYear' => 'required|integer|min:1900|max:2030',
            'endYear' => 'nullable|integer|min:1900|max:2030|gte:startYear',
            'degree' => 'required|string|in:primary,gymnasium,secondary,vocational,bachelor,engineer,master,doctor',
            'schoolName' => 'required|string|max:255',
            'schoolAddress' => 'nullable|string|max:500',
            'isOngoing' => 'boolean',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

dd($validatedData);

        // Tymczasowo wyświetlamy dane
        return redirect()->route('employee.education.show')->with('success', 'Edukacja została pomyślnie dodana!');
    }

    public function updateEducation(Request $request)
    {
        dd('update education', $request->all());
    }
}
