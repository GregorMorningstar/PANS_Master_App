<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\LeavesServiceInterface;
use App\Enums\LeavesType;
use App\Enums\LeavesStatus;
use Carbon\Carbon;

class CalendarController extends Controller
{

    public function __construct(private readonly LeavesServiceInterface $leavesService)
    {}


    public function index()
    {

        $userleavesById = $this->leavesService->getAllLeavesByUserId(auth()->id());
        return inertia('employee/calendar/index', ['userleavesById' => $userleavesById]);
    }

    public function history()
    {
        return inertia('employee/calendar/history');
    }

    public function store(Request $request)
    {
        // Get valid enum values for validation
        $validTypes = array_map(fn($e) => $e->value, LeavesType::cases());

        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|string|in:' . implode(',', $validTypes),
            'description' => 'nullable|string|max:1000',
            'working_days_count' => 'nullable|integer|min:1',
        ]);

        // Check for overlapping leaves
        $existingLeaves = $this->leavesService->getAllLeavesByUserId($data['user_id']);
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        foreach ($existingLeaves as $leave) {
            $existingStart = Carbon::parse($leave->start_date);
            $existingEnd = Carbon::parse($leave->end_date);

            // Check if dates overlap
            if (($startDate <= $existingEnd) && ($endDate >= $existingStart)) {
                return back()->withErrors([
                    'leave_type' => 'Urlop nakłada się z istniejącym urlopem od ' .
                                   $existingStart->format('Y-m-d') . ' do ' . $existingEnd->format('Y-m-d')
                ])->withInput();
            }
        }

        // Calculate working days
        $workingDays = $this->calculateWorkingDays($startDate, $endDate);

        // Map frontend fields to backend expected format
        $mappedData = [
            'user_id' => $data['user_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'type' => $data['leave_type'],
            'description' => $data['description'] ?? null,
            'days' => $workingDays,
            'status' => LeavesStatus::PENDING->value,
        ];

        $this->leavesService->store($mappedData);

        return redirect()->route('employee.calendar.index')
                        ->with('success', 'Wniosek urlopu został złożony pomyślnie.');
    }

    /**
     * Calculate working days between two dates (excluding weekends)
     */
    private function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $current = $startDate->copy();

        while ($current <= $endDate) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($current->dayOfWeek !== Carbon::SATURDAY && $current->dayOfWeek !== Carbon::SUNDAY) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    public function show($id)
    {
        $leave = $this->leavesService->getLeavesById($id);
        $user = auth()->user();

        $moderator = \App\Models\User::where('role', 'moderator')->first();

        return inertia('employee/calendar/detalis-leaves', [
            'leave' => $leave,
            'currentUserRole' => $user->role ?? 'employee',
            'currentUserId' => $user->id,
            'moderatorId' => $moderator?->id,
        ]);
    }
}
