<?php

namespace App\Repositories\Eloquent;

use App\Models\Leaves;
use App\Repositories\Contracts\LeavesRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use App\Models\LeaveBalance;


class EloquentLeavesRepository implements LeavesRepositoryInterface
{
    public function __construct(private readonly Leaves $leaves,
     private readonly LeaveBalance $leaveBalance)
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
     */
    public function getPendingLeaves(): Collection
    {
        return $this->leaves
            ->with('user')
            ->where('status', 'pending')
            ->orderBy('start_date', 'desc')
            ->get();
    }
    public function getLeaveByIdAndYear(int $leaveId, int $year): ?Leaves
    {
        return $this->leaves
            ->where('id', $leaveId)
            ->whereYear('start_date', $year)
            ->first();
    }

    /**
     * Zatwierdź urlop i zaktualizuj saldo
     */
    public function approveLeave(int $leaveId, int $approvedBy, ?string $description = null, array $meta = []): bool
    {

        $leavesByIdAndYear = $this->getLeaveByIdAndYear($leaveId, now()->year);
    dd($leavesByIdAndYear);  // tylko debug: pokaż tylko opis (i ew. meta) w dd
        dd([
            'leave_id'    => $leaveId,
            'approved_by' => $approvedBy,
            'description' => $description,
            'meta'        => $meta,
        ]);
    }

    /**
     * Odrzuć urlop
     */
    public function rejectLeave( int $leaveId, int $rejectedBy, string $rejectionReason, array $meta = []): bool
    {
        $leave = $this->leaves->find($leaveId);
        if (!$leave || $leave->status !== 'pending') {
            return false;
        }

        $update = [
            'status' => 'rejected',
            'approved_by' => $rejectedBy,
            'approved_at' => now(),
            'rejection_reason' => $rejectionReason
        ];

        if (isset($meta['days'])) {
            $update['days'] = (int) $meta['days'];
        }
        if (isset($meta['type'])) {
            $update['type'] = $meta['type'];
        }
        if (isset($meta['user_id'])) {
            $update['user_id'] = (int) $meta['user_id'];
        }

        return $leave->update($update);
    }

    /**
     * Pobierz oczekujące urlopy dla konkretnego użytkownika
     */
    public function getUserPendingLeaves(int $userId): Collection
    {
        return $this->leaves
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Pobierz użyte urlopy konkretnego użytkownika
     */
    public function getUsedLeavesByUser(int $userId): Collection
    {
        $today = Carbon::today();

        return $this->leaves
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('start_date', '<', $today)
            ->orderBy('start_date', 'desc')
            ->get();
    }
}


