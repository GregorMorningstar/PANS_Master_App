<?php


namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Contracts\FlagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class EloquentFlagRepository implements FlagRepositoryInterface
{
    public function __construct(private readonly User $user,
                                private readonly UserProfile $userProfile)
    {
    }

    // zwraca kolekcję rekordów
    public function addressFlagsByUser(): Collection
    {
        $address = $this->userProfile->where('user_id', Auth::id())->get();
        $addresCount = $address->count();

        if ($addresCount > 0) {
            $user = $this->user->find(Auth::id());
          //  dd($user);
            $user->is_complited_address = true;
            $user->save();

            return $address;
        } else {
            $user = $this->user->find(Auth::id());
            $user->is_complited_address = false;
            $user->save();

        }

        return collect();
    }
}
