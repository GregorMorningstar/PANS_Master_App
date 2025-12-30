<?php


namespace App\Services\Contracts;


interface UserProfileServiceInterface
{
    public function checkProfileCompletion(int $userId): bool;

}
