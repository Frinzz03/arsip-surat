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
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_agenda', 50)->unique();
            $table->string('kode_surat', 100)->index();
            $table->string('nomor_surat', 255)->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->date('tanggal_masuk')->index();
            $table->string('pengirim', 255)->nullable();
            $table->enum('sifat', ['biasa', 'penting', 'segera', 'rahasia'])->default('biasa');
            $table->text('perihal')->nullable();
            $table->string('hari_acara', 100)->nullable();
            $table->date('tanggal_acara')->nullable();
            $table->string('waktu_acara', 50)->nullable();
            $table->string('tempat_acara', 255)->nullable();
            $table->string('penerima', 255);
            $table->string('file_path', 500)->nullable();
            $table->string('file_original_name', 255)->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Composite index for combined filters
            $table->index(['tanggal_masuk', 'pengirim']);
        });

        // Add FULLTEXT index on perihal for BM25-style search
        DB::statement('ALTER TABLE surat_masuk ADD FULLTEXT INDEX ft_perihal (perihal)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
