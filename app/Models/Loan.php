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

    public function getLateDurationAttribute()
    {
        $dueDate = Carbon::parse($this->tanggal_kembali_rencana);
        $compareDate = $this->tanggal_kembali_aktual ? Carbon::parse($this->tanggal_kembali_aktual) : Carbon::now();

        if ($compareDate->lte($dueDate)) {
            return null;
        }

        $daysLate = $dueDate->diffInDays($compareDate);

        if ($daysLate < 30) {
            return $daysLate . ' hari';
        }

        if ($daysLate < 365) {
            $monthsLate = intdiv($daysLate, 30);
            return $monthsLate . ' bulan';
        }

        $yearsLate = intdiv($daysLate, 365);
        return $yearsLate . ' tahun';
    }

    public function getReturnPhotoUrlAttribute()
    {
        return $this->return_photo_path ? asset('storage/' . $this->return_photo_path) : null;
    }
} 