<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi')->unique();
            $table->string('nama');
            $table->string('merek');
            $table->string('model');
            $table->year('tahun');
            $table->enum('jenis', ['Mobil', 'Truk', 'Motor', 'Trailer']);
            $table->foreignId('bbm_id')->constrained('bbm')->onDelete('cascade');
            $table->integer('kapasitas_tangki')->default(60);
            $table->decimal('konsumsi_bbm_standar', 10, 2)->default(1.5);
            $table->decimal('odometer_awal')->nullable();
            $table->decimal('odometer_terakhir')->nullable();
            $table->string('departemen');
            $table->string('pengemudi_default')->nullable();
            $table->enum('status', ['Aktif', 'Non_Aktif', 'Dijual']);
            $table->softDeletes();
            $table->index('status');
            $table->index('departemen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
