<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test petugas dashboard
    view('petugas.dashboard');
    echo "✓ Petugas dashboard template OK\n";
} catch (Exception $e) {
    echo "✗ Petugas dashboard error: " . $e->getMessage() . "\n";
}

try {
    // Test admin returns
    view('admin.returns.index');
    echo "✓ Admin returns template OK\n";
} catch (Exception $e) {
    echo "✗ Admin returns error: " . $e->getMessage() . "\n";
}

try {
    // Test peminjam riwayat
    view('peminjam.riwayat');
    echo "✓ Peminjam riwayat template OK\n";
} catch (Exception $e) {
    echo "✗ Peminjam riwayat error: " . $e->getMessage() . "\n";
}

echo "\nAll templates compiled successfully!\n";
?>
