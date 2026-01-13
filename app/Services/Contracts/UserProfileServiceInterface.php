<?php

namespace App\Services\Contracts;

interface UserProfileServiceInterface
{
    public function checkProfileCompletion(int $userId): bool;

    public function getCompanyDataByNip(string $nip): array;

    public function validateNip(string $nip): bool;
    public function getUserProfileByUserId(int $userId): ?array;
}
