<?php

namespace App\Services;

use App\Services\Contracts\FlagServiceInterface;
use App\Repositories\Contracts\FlagRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FlagService implements FlagServiceInterface
{
    public function __construct(private readonly FlagRepositoryInterface $flagRepository)
    {
    }

    public function getAddressFlagsForUser(): array
    {
        $profiles = $this->flagRepository->addressFlagsByUser();
       
    }
}
