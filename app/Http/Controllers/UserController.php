<?php

namespace App\Http\Controllers;

use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\EducationServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(private readonly UserServiceInterface $users,
                                private readonly EducationServiceInterface $educationService)
    {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 6);
        $filters = [
            'name' => $request->query('name'),
            'department' => $request->query('department'),
            'barcode' => $request->query('barcode'),
        ];

        $users = $this->users->getAllByRole($perPage, 'employee', $filters);

        return Inertia::render('moderator/user/index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function show(int $id)
    {
        $user = $this->users->getById($id);
        dd($user);
        return Inertia::render('moderator/user/show', ['user' => $user]);
    }

    public function confirmationEducation(Request $request)
    {
        $filters = [
            'name'      => $request->query('name'),
            'school'    => $request->query('school'),
            'year_from' => $request->query('year_from'),
            'year_to'   => $request->query('year_to'),
            'per_page'  => (int) $request->query('per_page', 6),
        ];

        $pendingCertificates = $this->educationService->getAllPendingCertificates($filters);

        return Inertia::render('moderator/user/education/confirmation-education', [
            'pendingCertificates' => $pendingCertificates,
            'filters' => $filters,
        ]);
    }

    public function confirmationWorkCertificates(Request $request)
    {

    $filters = [
            'name'         => $request->query('name'),
            'company_name' => $request->query('company_name'),
            'nip'          => $request->query('nip'),
            'start_date_from' => $request->query('start_date_from'),
            'start_date_to'   => $request->query('start_date_to'),
            'end_date_from'   => $request->query('end_date_from'),
            'end_date_to'     => $request->query('end_date_to'),
            'per_page'     => (int) $request->query('per_page', 6),
        ];

        $getAllCertificatesWithPendingStatus = $this->users->getAllCertificatesWithPendingStatus($filters);
      // dd($getAllCertificatesWithPendingStatus);
        return Inertia::render('moderator/user/work/confirmation-work-certificates', [
            'pendingCertificates' => $getAllCertificatesWithPendingStatus,
            'filters' => $filters,
        ]);
    }
}
