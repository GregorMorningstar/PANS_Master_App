<?php

namespace App\Services;

use App\Services\Contracts\UserProfileServiceInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Models\User;
use App\Services\NipService;
use Illuminate\Support\Facades\Auth;
class UserProfileService implements UserProfileServiceInterface
{
    public function __construct(
        private readonly UserProfileRepositoryInterface $userProfileRepository,
        private readonly NipService $nipService
    ) {
    }


    public function checkProfileCompletion(int $userId): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $isEducationComplete = $this->userProfileRepository->checkEducationCompletion($user);
        $isWorkTimeComplete = $this->userProfileRepository->checkWorkTimeCompletion($user);
        $isAddressComplete = $this->userProfileRepository->checkAddressCompletion($user);

        return $isEducationComplete && $isWorkTimeComplete && $isAddressComplete;
    }

    /**
     * Pobiera dane firmy na podstawie numeru NIP
     */
    public function getCompanyDataByNip(string $nip): array
    {
        return $this->nipService->fetchByNip($nip);
    }

    /**
     * Waliduje numer NIP
     */
    public function validateNip(string $nip): bool
    {
        return $this->nipService->validateNip($nip);
    }

    public function getUserProfileByUserId(?int $userId = null): ?array
    {
        $userId = $userId ?? Auth::id();
        if (! $userId) {
            return null;
        }

        $profile = $this->userProfileRepository->getAdressByUserId($userId);
        if (! $profile) {
            return null;
        }

        // zwracamy atrybuty modelu jako tablicę dla serwisów
        return $profile->toArray();
    }
}
