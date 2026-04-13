<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Loan;

// Rollback loan 8 back to disetujui (undo test changes)
$loan = Loan::find(8);
if ($loan && $loan->status == 'dikembalikan') {
    $loan->update([
        'status' => 'disetujui',
        'tanggal_kembali_aktual' => null,
    ]);
    $loan->refresh();
    echo "Loan ID 8 has been reset to status: {$loan->status}" . PHP_EOL;
} else {
    echo "Loan ID 8 status is already: " . ($loan->status ?? 'N/A') . PHP_EOL;
}
?>
