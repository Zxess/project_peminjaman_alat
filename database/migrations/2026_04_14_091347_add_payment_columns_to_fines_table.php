<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            // Tambahkan kolom yang diperlukan untuk Midtrans
            if (!Schema::hasColumn('fines', 'order_id')) {
                $table->string('order_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('fines', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('status');
            }
            // payment_date sudah ada di create_fines_table migration
        });
    }

    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            if (Schema::hasColumn('fines', 'order_id')) {
                $table->dropColumn('order_id');
            }
            if (Schema::hasColumn('fines', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};