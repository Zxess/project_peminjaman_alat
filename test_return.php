<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Find a borrower and an approved loánwith their user
$loan = Loan::where('status', 'disetujui')->first();

if (!$loan) {
    echo "No approved loans found to test" . PHP_EOL;
    exit;
}

echo "Testing requestReturn for Loan ID: {$loan->id}" . PHP_EOL;
echo "- Original Status: {$loan->status}" . PHP_EOL;
echo "- User ID: {$loan->user_id}" . PHP_EOL;

// Verify before update
echo "Before update:" . PHP_EOL;
echo "- Status: {$loan->status}" . PHP_EOL;
echo "- tanggal_kembali_aktual: {$loan->tanggal_kembali_aktual}" . PHP_EOL;

// Attempt the update that requestReturn() would do
$loan->update([
    'status' => 'dikembalikan',
    'tanggal_kembali_aktual' => now(),
]);

// Verify after update
$loan->refresh();
echo "\nAfter update:" . PHP_EOL;
echo "- Status: {$loan->status}" . PHP_EOL;
echo "- tanggal_kembali_aktual: {$loan->tanggal_kembali_aktual}" . PHP_EOL;
echo "\nTest completed successfully!" . PHP_EOL;
?>
