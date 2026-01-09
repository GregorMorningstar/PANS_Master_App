<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Contracts\LeavesServiceInterface;
use Carbon\Carbon;

class ModeratorController extends Controller
{
    public function __construct(private readonly LeavesServiceInterface $leavesInterface)
    {
    }

    public function dashboard()
    {
       return Inertia::render('moderator/dashboard/index');
    }

    public function getAllLeavels()
    {
        return Inertia::render('moderator/users/levels/index');
    }

    /**
     * Wyświetl kalendarz urlopów wszystkich pracowników
     */
    public function getLeavesCalendar(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $leaves = $this->leavesInterface->getCalendarLeaves($startDate, $endDate);
        $statistics = $this->leavesInterface->getLeavesStatistics();
        $activeLeaves = $this->leavesInterface->getActiveLeaves();

        return Inertia::render('moderator/user/leaves/index', [
            'leaves' => $leaves,
            'statistics' => $statistics,
            'activeLeaves' => $activeLeaves,
            'currentDate' => now()->format('Y-m-d'),
        ]);
    }


    public function getPendingLeaves()
    {
        $pendingLeaves = $this->leavesInterface->getPendingLeaves();

        return Inertia::render('moderator/user/leaves/pending', [
            'pendingLeaves' => $pendingLeaves,
        ]);
    }

    /**
     * Zatwierdź urlop
     */
    public function approveLeave(Request $request, int $leaveId)
    {
        $request->validate([
            'description' => 'required|string|min:1|max:1000'
        ]);

        $description = $request->input('description');
        $approvedBy = auth()->id();

        // opcjonalne dodatkowe dane przesyłane z frontendu
        $meta = $request->only(['days', 'type', 'user_id', 'year']);

        $success = $this->leavesInterface->approveLeave($leaveId, $approvedBy, $description, $meta);

        if ($success) {
            return redirect()->back()->with('success', 'Urlop został zatwierdzony.');
        }

        return redirect()->back()->with('error', 'Nie udało się zatwierdzić urlopu.');
    }

    /**
     * Odrzuć urlop
     */
    public function rejectLeave(Request $request, int $leaveId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:1|max:1000'
        ]);

        $rejectionReason = $request->input('rejection_reason');
        $rejectedBy = auth()->id();

        $meta = $request->only(['days', 'type', 'user_id', 'year']);

        $success = $this->leavesInterface->rejectLeave($leaveId, $rejectedBy, $rejectionReason, $meta);

        if ($success) {
            return redirect()->back()->with('success', 'Urlop został odrzucony.');
        }

        return redirect()->back()->with('error', 'Nie udało się odrzucić urlopu.');
    }

    /**
     * Pobierz oczekujące urlopy konkretnego użytkownika
     */
    public function getUserPendingLeaves(int $userId)
    {
        $pendingLeaves = $this->leavesInterface->getUserPendingLeaves($userId);

        return Inertia::render('moderator/user/leaves/user-pending', [
            'pendingLeaves' => $pendingLeaves,
            'userId' => $userId,
        ]);
    }
}
