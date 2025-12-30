<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'pesel',
        'address',
        'profile_photo',
        'birth_date',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected static function booted()
    {
        static::deleting(function ($profile) {
            // Usuń zdjęcie profilowe przy usuwaniu profilu
            if ($profile->profile_photo && Storage::disk('public')->exists($profile->profile_photo)) {
                Storage::disk('public')->delete($profile->profile_photo);
            }
        });
    }

    /**
     * Relacja do użytkownika
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor dla pełnej ścieżki zdjęcia profilowego
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo) {
            return null;
        }

        if (Storage::disk('public')->exists($this->profile_photo)) {
            return Storage::disk('public')->url($this->profile_photo);
        }

        return null;
    }

    /**
     * Sprawdź czy profil jest kompletny
     */
    public function isComplete(): bool
    {
        return !empty($this->phone) &&
               !empty($this->pesel) &&
               !empty($this->address) &&
               !empty($this->birth_date);
    }

    /**
     * Oblicz procent ukończenia profilu
     */
    public function getCompletionPercentage(): float
    {
        $fields = ['phone', 'pesel', 'address', 'birth_date', 'profile_photo'];
        $completed = 0;

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100, 2);
    }
}
