<?php

namespace App\Services\Contracts;

use App\Models\User;

interface FlagServiceInterface
{
 /**
     * Sprawdza, czy $user może wykonać $action na danym $resource.
     * $action: 'view'|'edit'|'delete'|'create' itd.
     */
public function getRole(?User $user): ?string;
}
