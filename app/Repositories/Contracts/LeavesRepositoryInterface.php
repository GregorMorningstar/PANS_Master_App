<?php

namespace App\Repositories\Contracts;
use App\Models\Leaves;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

interface LeavesRepositoryInterface
{
    public function getAllLeavesByUserId(int $userId);
    public function store(array $data);
    public function getLeavesById(int $id): ? Leaves ;
    public function updateLeave(int $id, array $data);
    public function deleteLeave(int $id): bool;
    public function getAllLeaves();

    /**
     * Pobierz wszystkie urlopy w określonym przedziale dat
     */
    public function getLeavesInDateRange(Carbon $startDate, Carbon $endDate): Collection;

    /**
     * Pobierz urlopy dla konkretnego użytkownika w przedziale dat
     */
    public function getUserLeaves(int $userId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection;

    /**
     * Pobierz wszystkie aktywne urlopy
     */
    public function getActiveLeaves(): Collection;

    /**
     * Pobierz urlopy według statusu
     */
    public function getLeavesByStatus(string $status): Collection;

    /**
     * Pobierz statystyki urlopów
     */
    public function getLeavesStatistics(): array;

    /**
     * Pobierz wszystkie oczekujące urlopy
     */
    public function getPendingLeaves(): Collection;
}
