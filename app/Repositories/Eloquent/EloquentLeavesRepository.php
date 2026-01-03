<?php

namespace App\Repositories\Eloquent;

use App\Models\Leaves;
use App\Repositories\Contracts\LeavesRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class EloquentLeavesRepository implements LeavesRepositoryInterface
{
    public function __construct(private readonly Leaves $leaves)
    {
    }

    public function getAllLeavesByUserId(int $userId): Collection
    {
        return $this->leaves->where('user_id', $userId)->with('user')->get();
    }

    public function store(array $data): Leaves
    {
        return $this->leaves->create($data);
    }

    public function getLeavesById(int $id): ?Leaves
    {
        return $this->leaves->with('user')->find($id);
    }

    public function updateLeave(int $id, array $data): bool
    {
        $leave = $this->leaves->find($id);
        if (!$leave) {
            return false;
        }
        return $leave->update($data);
    }

    public function deleteLeave(int $id): bool
    {
        $leave = $this->leaves->find($id);
        if (!$leave) {
            return false;
        }
        return $leave->delete();
    }

    public function getAllLeaves(): Collection
    {
        return $this->leaves->with('user')->orderBy('start_date', 'desc')->get();
    }

    /**
     * Pobierz wszystkie urlopy w określonym przedziale dat
     */
    public function getLeavesInDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->leaves
            ->with('user')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Pobierz urlopy dla konkretnego użytkownika w przedziale dat
     */
    public function getUserLeaves(int $userId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = $this->leaves->with('user')->where('user_id', $userId);

        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($subQ) use ($startDate, $endDate) {
                      $subQ->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
                  });
            });
        }

        return $query->orderBy('start_date')->get();
    }

    /**
     * Pobierz wszystkie aktywne urlopy
     */
    public function getActiveLeaves(): Collection
    {
        $today = Carbon::today();
        return $this->leaves
            ->with('user')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('status', 'approved')
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Pobierz urlopy według statusu
     */
    public function getLeavesByStatus(string $status): Collection
    {
        return $this->leaves
            ->with('user')
            ->where('status', $status)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Pobierz statystyki urlopów
     */
    public function getLeavesStatistics(): array
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->startOfMonth();
        $nextMonth = Carbon::now()->addMonth()->endOfMonth();

        return [
            'total_leaves' => $this->leaves->count(),
            'pending_leaves' => $this->leaves->where('status', 'pending')->count(),
            'approved_leaves' => $this->leaves->where('status', 'approved')->count(),
            'rejected_leaves' => $this->leaves->where('status', 'rejected')->count(),
            'active_leaves' => $this->leaves
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->where('status', 'approved')
                ->count(),
            'upcoming_leaves' => $this->leaves
                ->where('start_date', '>', $today)
                ->where('start_date', '<=', $nextMonth)
                ->where('status', 'approved')
                ->count(),
        ];
    }

    /**
     * Pobierz wszystkie oczekujące urlopy
     */    public function getPendingLeaves(): Collection
    {
        return $this->leaves
            ->with('user')
            ->where('status', 'pending')
            ->orderBy('start_date', 'desc')
            ->get();
    }
}


