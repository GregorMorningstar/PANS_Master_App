<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Leaves;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'leave_balances';

    protected $fillable = [
        'user_id',
        'year',
        'leave_type',
        'barcode',
        'entitlement_days',
        'used_days',
        'remaining_days',
        'request_days',
        'request_used',
        'seniority_years',
        'employment_start_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leaves::class, 'leave_balance_id');
    }
protected static function booted()
    {
        static::created(function ($leavesBalance) {
            $prefix = '4100';
            $id = $leavesBalance->id;
            $barcode = $prefix . str_pad($id, 13 - strlen($prefix), '0', STR_PAD_LEFT);
            if ($leavesBalance->barcode !== $barcode) {
                $leavesBalance->barcode = $barcode;
                $leavesBalance->save();
            }
        });

    }
}
