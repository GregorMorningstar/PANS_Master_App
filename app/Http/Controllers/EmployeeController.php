<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\EducationServiceInterface;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Repositories\Contracts\FlagRepositoryInterface;
use App\Services\Contracts\EmploymentCertificateServiceInterface;
use Inertia\Inertia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly UserProfileServiceInterface $userProfileService,
        private readonly FlagRepositoryInterface $flagRepository,
        private readonly EducationServiceInterface $educationService,
        private readonly EmploymentCertificateServiceInterface $employmentCertificateService
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
        $address = $this->userProfileService->getAddressData();

        return Inertia::render('employee/profile/show', [
            'address' => $address,
        ]);
    }

    public function showAddress()
    {
        $address = $this->userProfileService->getAddressData();
        $user = Auth::user();

        return Inertia::render('employee/address/show', [
            'address' => $address,
            'user' => $user,
        ]);
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

        // obsługa zdjęcia profilowego przez serwis
        if ($request->hasFile('profile_photo')) {
            /** @var UploadedFile $file */
            $file = $request->file('profile_photo');
            $path = $this->userProfileService->storeProfilePhoto($file, $user);
            $validated['profile_photo'] = $path;
        }

        $profile = $this->userProfileService->getProfile($user);

        if ($profile) {
            $this->userProfileService->updateProfile($profile, $validated);
        } else {
            $this->userProfileService->createProfile($user, $validated);
        }


        $this->flagRepository->addressFlagsByUser();

        return redirect()->route('employee.profile.show')->with('success', 'Profil został zapisany.');
    }

    public function showCompany()
    {

    $company = $this->employmentCertificateService->getCertificatesByUserId(Auth::id());
        return Inertia::render('employee/company/show', [
            'company' => $company,
        ]);
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


        $validatedData = $request->validate([

            'nip' => 'required|string|size:10',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'street' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'workCertificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $user = Auth::user();

        // map incoming request fields to model fillable keys and include user_id
        $payload = [
            'user_id' => $user->id,
            'nip' => $validatedData['nip'],
            'company_name' => $validatedData['name'] ?? null,
            'street' => $validatedData['street'] ?? null,
            'zip_code' => $validatedData['zip'] ?? null,
            'city' => $validatedData['city'] ?? null,
            'start_date' => $request->input('workStartDate'),
            'end_date' => $request->input('workEndDate'),
            'position' => $request->input('position'),
            'additional_info' => $request->input('jobDescription'),
        ];

        // handle uploaded work certificate file
        if ($request->hasFile('workCertificate')) {
            $file = $request->file('workCertificate');
            $extension = $file->getClientOriginalExtension();
            $uniqueName = 'work_certificate_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $extension;
            $path = $file->storeAs('work_certificates', $uniqueName, 'public');
            $payload['work_certificate_file_path'] = $path;
        }

        $work_certification = $this->employmentCertificateService->employeeCreateCertificate($payload);

        if ($work_certification) {
            $this->flagRepository->employmentFlagsByUser();
        } else {
            return back()->withErrors([
                'company' => 'Wystąpił błąd podczas zapisywania firmy.'
            ]);
        }


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

        $user = Auth::user();

        // Handle certificate file upload
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueName = 'certificate_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $extension;
            $path = $file->storeAs('certificates', $uniqueName, 'public');
            $validatedData['certificate_path'] = $path;
        }

        $education = $this->educationService->createSchoolCertificate($user, $validatedData);
        if($education) {
            $this->flagRepository->educationFlagsByUser();
        } else {
            return back()->withErrors([
                'education' => 'Wystąpił błąd podczas zapisywania edukacji.'
            ]);
        }
        // Tymczasowo wyświetlamy dane
        return redirect()->route('employee.education.show')->with('success', 'Edukacja została pomyślnie dodana!');
    }

    public function updateEducation(Request $request)
    {
        dd('update education', $request->all());
    }

    public function showEducationDetails(Request $request)
    {
        $user = Auth::user();
        $page = (int) $request->get('page', 1);
       $maxEducation = $this->educationService->getMaximumEducationLevelForUser($user->id);
      //  dd($maxEducation);
        // get paginated certificates for current user (4 per page)
        $certificates = $this->educationService->getPaginatedCertificatesForUser($user, 4);

        // get current certificate - for now show the first one from current page
        $education = $certificates->items()[0] ?? null;

        // prepare pagination data for individual certificate navigation
        $pagination = null;
        if ($certificates->total() > 1) {
            $currentPage = $certificates->currentPage();
            $totalPages = $certificates->lastPage();

            $pagination = [
                'prev_url' => $currentPage > 1 ?
                    route('employee.education.show', ['page' => $currentPage - 1]) :
                    null,
                'next_url' => $currentPage < $totalPages ?
                    route('employee.education.show', ['page' => $currentPage + 1]) :
                    null,
                'current' => $currentPage,
                'total' => $totalPages
            ];
        }

        return Inertia::render('employee/education/show-details', [
            'education' => $education,
            'pagination' => $pagination,
        ]);
    }
}
