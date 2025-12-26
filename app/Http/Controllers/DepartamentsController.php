<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\DepartmentsService;
class DepartamentsController extends Controller
{

    public function __construct(private readonly DepartmentsService $departmentsService)
    {
    }


    public function moderatorIndex(Request $request)
    {
        $departments = $this->departmentsService->getAllDepartmentsWithUsersPaginated(6);
//dd($departments);
        return Inertia::render('moderator/departments/index', [
            'departments' => $departments,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
            'department_id' => $request->session()->get('department_id'),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('moderator/departments/create');
    }
    public function edit(Request $request, int $id)
    {
        $dept = $this->departmentsService->getById($id);
        if (!$dept) abort(404);
        return Inertia::render('moderator/departments/edit', [
            'department' => $dept,
        ]);
    }
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        // If name changed, check uniqueness
        $existing = $this->departmentsService->getById($id);
        if (!$existing) abort(404);
        if ($existing->name !== $validated['name'] && $this->departmentsService->existsByName($validated['name'])) {
            return back()->withErrors(['name' => 'Dział o takiej nazwie już istnieje.'])->withInput();
        }

        $this->departmentsService->updateDepartment($id, $validated);

        return redirect()->route('moderator.departments.index')->with('success', 'Dział został zaktualizowany.')->with('department_id', $id);
    }
    public function destroy(Request $request, int $id)
    {
        $dept = $this->departmentsService->getById($id);
        if (!$dept) {
            return redirect()->route('moderator.departments.index')->with('error', 'Dział nie istnieje.');
        }

        // Prevent deletion if department still has related users or machines/counts
        $hasUsers = $dept->users()->exists();
        $hasMachines = (int) ($dept->count_of_machine ?? 0) > 0;
        $hasEmployees = (int) ($dept->count_of_employee ?? 0) > 0;
        $hasFailures = (int) ($dept->count_of_failure_machine ?? 0) > 0;

        if ($hasUsers || $hasMachines || $hasEmployees || $hasFailures) {
            return redirect()->route('moderator.departments.index')->with('error', 'Nie można usunąć działu. Najpierw odpiąć pracowników i maszyny lub wyczyścić powiązane rekordy.');
        }

        $deleted = $this->departmentsService->deleteDepartment($id);
        if ($deleted) {
            return redirect()->route('moderator.departments.index')->with('success', 'Dział został usunięty.');
        }
        return redirect()->route('moderator.departments.index')->with('error', 'Nie udało się usunąć działu.');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        // Check existence via service (prevents race if you prefer manual check vs validation rule)
        if ($this->departmentsService->existsByName($validated['name'])) {
            return back()->withErrors(['name' => 'Dział o takiej nazwie już istnieje.'])->withInput();
        }

        // Create department
        $dept = $this->departmentsService->createDepartment($validated);

        return redirect()->route('moderator.departments.index')->with('success', 'Dział został pomyślnie dodany.')->with('department_id', $dept->id);
    }


    public function show(int $id)
    {
        $department = $this->departmentsService->getByIdWithUsersAndMachines($id);
        dd($department);
        if (!$department) {
            abort(404, 'Dział nie istnieje.');
        }

        return Inertia::render('moderator/departments/show', [
            'department' => $department,
        ]);
    }
}
