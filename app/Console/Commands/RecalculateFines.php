<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalculateFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-fines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate fines for all returned loans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $loans = \App\Models\Loan::where('status', 'kembali')->whereNotNull('tanggal_kembali_aktual')->get();

        $count = 0;
        foreach ($loans as $loan) {
            $fineAmount = $loan->calculateFine();
            if ($fineAmount > 0) {
                \App\Models\Fine::firstOrCreate([
                    'loan_id' => $loan->id,
                ], [
                    'amount' => $fineAmount,
                    'status' => 'pending',
                    'reason' => 'Keterlambatan pengembalian alat'
                ]);
                $count++;
                $this->info("Added fine for loan ID {$loan->id}: Rp " . number_format($fineAmount));
            }
        }

        $this->info("Recalculation complete. Added fines to {$count} loans.");
    }
}
