<?php

namespace App\Services;

use App\Services\Contracts\LeavesServiceInterface;
use App\Repositories\Contracts\LeavesRepositoryInterface;
use Carbon\Carbon;

class LeavesService implements LeavesServiceInterface
{
    public function __construct(private readonly LeavesRepositoryInterface $leavesRepository)
    {
    }

    public function getAllLeavesByUserId(int $userId)
    {
        return $this->leavesRepository->getAllLeavesByUserId($userId);
    }

    public function store(array $data)
    {
        return $this->leavesRepository->store($data);
    }

    public function getLeavesById(int $id): ?\App\Models\Leaves
    {
        return $this->leavesRepository->getLeavesById($id);
    }

    public function updateLeave(int $id, array $data)
    {
        return $this->leavesRepository->updateLeave($id, $data);
    }

    public function deleteLeave(int $id): bool
    {
        return $this->leavesRepository->deleteLeave($id);
    }

    public function getAllLeaves()
    {
        return $this->leavesRepository->getAllLeaves();
    }

    /**
     * Pobierz urlopy w przedziale dat dla kalendarza
     */
    public function getCalendarLeaves(?string $startDate = null, ?string $endDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        return $this->leavesRepository->getLeavesInDateRange($start, $end);
    }

    /**
     * Pobierz statystyki urlopów dla dashboardu
     */
    public function getLeavesStatistics(): array
    {
        return $this->leavesRepository->getLeavesStatistics();
    }

    /**
     * Pobierz aktywne urlopy
     */
    public function getActiveLeaves()
    {
        return $this->leavesRepository->getActiveLeaves();
    }

    /**
     * Pobierz urlopy według statusu
     */
    public function getLeavesByStatus(string $status)
    {
        return $this->leavesRepository->getLeavesByStatus($status);
    }
}
