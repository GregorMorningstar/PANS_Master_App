<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\UserServiceInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Repositories\Contracts\FlagRepositoryInterface;
use Inertia\Inertia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{


    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly UserProfileRepositoryInterface $userProfileRepository,
        private readonly FlagRepositoryInterface $flagRepository
    ) {}

    public function index()
    {
        // poprawione wywołanie repozytorium (bez parametru, repo korzysta z Auth::id())
        $userFlagByAddress = $this->flagRepository->addressFlagsByUser();

        return Inertia::render('employee/dashboard/index', [
            'addressFlags' => $userFlagByAddress,
        ]);
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
        $user = Auth::user();

        $validated = $request->validate([
            'phone' => 'nullable|string|max:15',
            'pesel' => 'nullable|string|size:11',
            'address' => 'required|string|max:1000',
            'profile_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:15',
        ]);

        // obsługa zdjęcia profilowego przez repozytorium
        if ($request->hasFile('profile_photo')) {
            /** @var UploadedFile $file */
            $file = $request->file('profile_photo');
            $path = $this->userProfileRepository->storeProfilePhoto($file, $user);
            $validated['profile_photo'] = $path;
        }

        $profile = $this->userProfileRepository->getProfile($user);

        if ($profile) {
            $this->userProfileRepository->updateProfile($profile, $validated);
        } else {
            $this->userProfileRepository->createProfile($user, $validated);
        }


        $this->flagRepository->addressFlagsByUser();

        return redirect()->route('employee.profile.show')->with('success', 'Profil został zapisany.');
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
