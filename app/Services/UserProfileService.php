<?php

namespace App\Services;


use App\Services\Contracts\UserProfileServiceInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Models\User;


class UserProfileService implements UserProfileServiceInterface
{
  public function __construct(private readonly UserProfileRepositoryInterface $userProfileRepository)
  {
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
}
