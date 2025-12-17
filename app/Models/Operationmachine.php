<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Machines;
use App\Models\User;

class Operationmachine extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'user_id',
        'operation_name',
        'description',
        'duration_minutes',
    ];

    protected static function booted()
    {
        static::created(function ($operationMachine) {
            $prefix = '3100';
            $idStr = (string) $operationMachine->id;
            $barcode = $prefix . str_pad($idStr, 13 - strlen($prefix), '0', STR_PAD_LEFT);

            if ($operationMachine->barcode !== $barcode) {
                $operationMachine->barcode = $barcode;
                $operationMachine->save();
            }
        });
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machines::class, 'machine_id');
    }

  
}
