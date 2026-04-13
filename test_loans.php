<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test retrieving approved loans
$loans = App\Models\Loan::where('status', 'disetujui')->with('user')->get();
echo "Total approved loans (disetujui): " . $loans->count() . PHP_EOL;
foreach($loans as $loan) {
    echo "- Loan ID: {$loan->id}, User: {$loan->user->name}, Status: {$loan->status}" . PHP_EOL;
}

// Check for returned requests
$requests = App\Models\Loan::where('status', 'dikembalikan')->with('user')->get();
echo "\nTotal return requests (dikembalikan): " . $requests->count() . PHP_EOL;
foreach($requests as $req) {
    echo "- Loan ID: {$req->id}, User: {$req->user->name}, Status: {$req->status}" . PHP_EOL;
}
?>
