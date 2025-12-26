<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Operationmachine;
use App\Models\User;

class Machines extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'serial_number',
        'barcode',
        'year_of_production',
        'description',
        'status',
        'image_path',
        'last_failure_date',
        'user_id',       // owner
        'department_id',
    ];


protected static function booted()
    {
        static::created(function ($user) {
            $prefix = '3000';
            $id = $user->id;
            $barcode = $prefix . str_pad($id, 13 - strlen($prefix), '0', STR_PAD_LEFT);
            if ($user->barcode !== $barcode) {
                $user->barcode = $barcode;
                $user->save();
            }
        });
    }

    // maszyna należy do jednego departamentu (1:N)
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The user that operates the machine.
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    // many-to-many: wiele użytkowników może być przypisanych do maszyny (pivot machine_user)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'machine_user', 'machine_id', 'user_id')->withTimestamps();
    }

    // many-to-many: wiele operacji może być przypisanych do maszyny (pivot machine_operation)
    public function operations(): HasMany
    {
        return $this->hasMany(Operationmachine::class, 'machine_id');
    }

    //relacja z MachineFailure (1:N)
    public function machineFailures(): HasMany
    {
        return $this->hasMany(MachineFailure::class, 'machine_id');
    }
}
