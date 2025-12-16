<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Machines extends Model
{
    use HasFactory;
    protected $fillable = [
        'barcode',
        'user_id',
        'last_failure_date',
        'image_path',
        'year_of_production',
        'name',
        'model',
        'serial_number',
        'description',
        'department_id',
        'status',
    ];

    protected static function booted()
    {
        static::created(function ($machine) {
            $prefix = '3000';
            $id = $machine->id;
            $barcode = $prefix . str_pad($id, 13 - strlen($prefix), '0', STR_PAD_LEFT);
            if ($machine->barcode !== $barcode) {
                $machine->barcode = $barcode;
                $machine->save();
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
