<?php

namespace App\Services\Contracts;
use App\Models\Leaves;
use Illuminate\Database\Eloquent\Collection;

interface LeavesServiceInterface
{
    public function getAllLeavesByUserId(int $userId);
    public function store(array $data);
    public function getLeavesById(int $id): ? Leaves ;
    public function updateLeave(int $id, array $data);
    public function deleteLeave(int $id): bool;
    public function getAllLeaves();

    /**
     * Pobierz urlopy w przedziale dat dla kalendarza
     */
    public function getCalendarLeaves(?string $startDate = null, ?string $endDate = null);

    /**
     * Pobierz statystyki urlopów dla dashboardu
     */
    public function getLeavesStatistics(): array;

    /**
     * Pobierz aktywne urlopy
     */
    public function getActiveLeaves();

    /**
     * Pobierz urlopy według statusu
     */
    public function getLeavesByStatus(string $status);

    /**
     * Pobierz wszystkie oczekujące urlopy
     */
    public function getPendingLeaves(): Collection;
}
