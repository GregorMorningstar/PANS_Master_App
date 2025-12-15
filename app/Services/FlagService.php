<?php

namespace App\Services;

use App\Services\Contracts\FlagServiceInterface;

class FlagService extends FlagServiceInterface
{
 public function getRole(?User $user): ?string
    {
        return $user?->role;
    }
}
