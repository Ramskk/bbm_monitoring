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
        Schema::create('mutasi_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_id')->constrained('stok')->onDelete('cascade');
            $table->foreignId('bbm_id')->constrained('bbm')->onDelete('cascade');
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->decimal('jumlah', 15, 2);
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->decimal('harga_per_liter', 15, 2)->default(0);
            $table->string('referensi_no')->nullable();
            $table->string('referensi_type')->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->index(['stok_id', 'jenis']);
            $table->index('tanggal');
            $table->index(['referensi_type', 'referensi_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok');
    }
};
