<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('loans')) {
            DB::statement("ALTER TABLE loans MODIFY status ENUM('pending','disetujui','ditolak','dikembalikan','kembali') NOT NULL DEFAULT 'pending'");
            Schema::table('loans', function (Blueprint $table) {
                $table->string('return_photo_path')->nullable()->after('tanggal_kembali_aktual');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('loans')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn('return_photo_path');
            });
            DB::statement("ALTER TABLE loans MODIFY status ENUM('pending','disetujui','ditolak','kembali') NOT NULL DEFAULT 'pending'");
        }
    }
};
