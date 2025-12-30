<?php


namespace App\Repositories\Eloquent;
use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class EloquentUserProfileRepository implements UserProfileRepositoryInterface
{
    public function checkEducationCompletion(User $user): bool
    {
        return (bool) ($user->is_complited_education ?? false);
    }

    public function checkWorkTimeCompletion(User $user): bool
    {
        return (bool) ($user->is_complited_work_time ?? false) &&
               !is_null($user->department_id);
    }

    public function checkAddressCompletion(User $user): bool
    {
        return (bool) ($user->is_complited_address ?? false);
    }

    public function createProfile(User $user, array $data): UserProfile
    {
        $data['user_id'] = $user->id;
        return UserProfile::create($data);
    }

    public function updateProfile(UserProfile $profile, array $data): UserProfile
    {
        $profile->update($data);
        return $profile->fresh();
    }

    public function getProfile(User $user): ?UserProfile
    {
        return $user->profile;
    }

    public function deleteProfile(UserProfile $profile): bool
    {
        return $profile->delete();
    }

    public function storeProfilePhoto(UploadedFile $file, User $user): string
    {
        // Usuń stare zdjęcie jeśli istnieje
        if ($user->profile && $user->profile->profile_photo) {
            $this->deleteProfilePhoto($user->profile->profile_photo);
        }

        // Zapisz nowe zdjęcie
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile_photos', $filename, 'public');

        return $path;
    }

    public function deleteProfilePhoto(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
