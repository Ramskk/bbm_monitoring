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
        Schema::create('transaksi_bbm', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->onDelete('restrict');
            $table->foreignId('bbm_id')->constrained('bbm')->onDelete('restrict');
            $table->foreignId('stok_id')->constrained('stok')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('jumlah_liter', 15, 2);
            $table->decimal('harga_per_liter', 15, 2);
            $table->decimal('total_biaya', 15, 2);
            $table->decimal('odometer_sebelum')->nullable();
            $table->decimal('odometer_sesudah');
            $table->decimal('jarak_tempuh')->nullable();
            $table->decimal('efisiensi', 10, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('alasan_reject')->nullable();
            $table->text('catatan')->nullable();
            $table->date('tanggal_pemakaian');
            $table->string('lokasi_pengisian')->nullable();
            $table->softDeletes();
            $table->index(['kendaraan_id', 'tanggal_pemakaian']);
            $table->index('status');
            $table->index('tanggal_pemakaian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_bbm');
    }
};
