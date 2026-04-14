<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = [
        'loan_id',
        'amount',
        'status',
        'reason',
        'order_id',        // Tambahkan ini
        'payment_method',  // Tambahkan ini
        'payment_date'     // Tambahkan ini
    ];

    // Relasi ke Loan
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}