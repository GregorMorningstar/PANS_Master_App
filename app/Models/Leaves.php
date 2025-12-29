<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Leaves extends Model
{
    use HasFactory;
    protected $table = 'leaves';

    protected $fillable = [
        'user_id',
        'barcode',
        'start_date',
        'end_date',
        'type',
        'description',
        'status',
        'approved_by',
        'approved_at',
    ];
protected static function booted()
    {
        static::created(function ($leaves) {
            $prefix = '4000';
            $id = $leaves->id;
            $barcode = $prefix . str_pad($id, 13 - strlen($prefix), '0', STR_PAD_LEFT);
            if ($leaves->barcode !== $barcode) {
                $leaves->barcode = $barcode;
                $leaves->save();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
