<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Contracts\LeavesServiceInterface;

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

    /**
     * Aktualizuj status urlopu
     */
    public function getPendingLeaves()
    {
        $pendingLeaves = $this->leavesInterface->getPendingLeaves();
       // dd($pendingLeaves);
        return Inertia::render('moderator/user/leaves/pending', [
            'pendingLeaves' => $pendingLeaves,
        ]);
    }
}
