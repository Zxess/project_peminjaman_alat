<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToFinesTable extends Migration
{
    public function up()
    {
        Schema::table('fines', function (Blueprint $table) {
            // Tambahkan kolom order_id jika belum ada
            if (!Schema::hasColumn('fines', 'order_id')) {
                $table->string('order_id')->nullable()->after('id');
            }
            
            // Tambahkan kolom lainnya jika diperlukan
            if (!Schema::hasColumn('fines', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('fines', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_at');
            }
            
            if (!Schema::hasColumn('fines', 'payment_transaction_id')) {
                $table->string('payment_transaction_id')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('fines', 'payment_order_id')) {
                $table->string('payment_order_id')->nullable()->after('payment_transaction_id');
            }
            
            if (!Schema::hasColumn('fines', 'payment_details')) {
                $table->json('payment_details')->nullable()->after('payment_order_id');
            }
            
            // Tambahkan index
            $table->index('order_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn([
                'order_id',
                'paid_at', 
                'payment_method', 
                'payment_transaction_id',
                'payment_order_id',
                'payment_details'
            ]);
        });
    }
}