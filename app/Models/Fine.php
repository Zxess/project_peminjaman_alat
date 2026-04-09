<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = [
        'loan_id',
        'amount',
        'status',
        'payment_date',
        'reason'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
