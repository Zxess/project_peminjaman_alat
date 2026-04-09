<?php 
namespace App\Models; 
use Illuminate\Database\Eloquent\Model; 
use Carbon\Carbon;
class Loan extends Model 
{ 
    protected $guarded = []; 
    public function user() { return $this->belongsTo(User::class); } 
    public function tool() { return $this->belongsTo(Tool::class); } 
    public function petugas() { return $this->belongsTo(User::class, 'petugas_id'); } 
    public function fines() { return $this->hasMany(Fine::class); }

    public function calculateFine()
    {
        if ($this->status !== 'kembali' || !$this->tanggal_kembali_aktual) {
            return 0;
        }

        $dueDate = Carbon::parse($this->tanggal_kembali_rencana);
        $returnDate = Carbon::parse($this->tanggal_kembali_aktual);

        if ($returnDate->lte($dueDate)) {
            return 0; // No fine if returned on time
        }

        $daysLate = $dueDate->diffInDays($returnDate);
        $finePerDay = 5000; // Rp 5,000 per day late
        return $daysLate * $finePerDay;
    }

    public function getTotalFineAttribute()
    {
        return $this->fines()->where('status', 'pending')->sum('amount');
    }
} 