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
            $table->string('order_id')->nullable()->after('id');
            $table->string('payment_method')->nullable()->after('status');
            $table->timestamp('payment_date')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'payment_method', 'payment_date']);
        });
    }
};