<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change sifat column: remove default, allow nullable
        DB::statement("ALTER TABLE surat_masuk MODIFY COLUMN sifat ENUM('biasa', 'penting', 'segera', 'rahasia') NULL DEFAULT NULL");

        // Allow tanggal_masuk to be nullable for imported data
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->date('tanggal_masuk')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE surat_masuk MODIFY COLUMN sifat ENUM('biasa', 'penting', 'segera', 'rahasia') NOT NULL DEFAULT 'biasa'");

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->date('tanggal_masuk')->nullable(false)->change();
        });
    }
};
