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
        Schema::create('po', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            $table->foreignId('vendor_id')->constrained('vendor')->onDelete('restrict');
            $table->foreignId('bbm_id')->constrained('bbm')->onDelete('restrict');
            $table->decimal('jumlah_liter', 15, 2);
            $table->decimal('harga_per_liter', 15, 2);
            $table->decimal('total_nilai', 15, 2);
            $table->integer('jumlah_diterima')->default(0);
            $table->date('tanggal_po');
            $table->date('tanggal_kirim_rencana')->nullable();
            $table->date('tanggal_kirim_aktual')->nullable();
            $table->date('tanggal_terima')->nullable();
            $table->enum('status', ['draft', 'approved', 'dikirim', 'diterima', 'closed', 'rejected'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('alasan_reject')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po');
    }
};
