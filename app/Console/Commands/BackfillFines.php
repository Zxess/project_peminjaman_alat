<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use App\Models\Fine;

class BackfillFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-fines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create fines for existing late returns that don\'t have fines yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for returned loans that need fines...');

        // Get all returned loans
        $returnedLoans = Loan::where('status', 'kembali')
                            ->whereNotNull('tanggal_kembali_aktual')
                            ->get();

        $finesCreated = 0;
        $totalFineAmount = 0;

        foreach ($returnedLoans as $loan) {
            // Check if this loan already has a fine
            $existingFine = Fine::where('loan_id', $loan->id)->first();

            if (!$existingFine) {
                // Calculate fine for this loan
                $fineAmount = $loan->calculateFine();

                if ($fineAmount > 0) {
                    Fine::create([
                        'loan_id' => $loan->id,
                        'amount' => $fineAmount,
                        'status' => 'pending',
                        'reason' => 'Keterlambatan pengembalian alat (backfill)'
                    ]);

                    $finesCreated++;
                    $totalFineAmount += $fineAmount;

                    $this->line("Created fine for Loan ID {$loan->id}: Rp " . number_format($fineAmount));
                }
            }
        }

        $this->info("Backfill complete!");
        $this->info("Fines created: {$finesCreated}");
        $this->info("Total fine amount: Rp " . number_format($totalFineAmount));
    }
}
