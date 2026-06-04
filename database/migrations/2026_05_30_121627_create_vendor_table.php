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
        Schema::create('vendor', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('alamat');
            $table->string('kota');
            $table->string('telepon');
            $table->string('email')->nullable();
            $table->string('npwp')->nullable();
            $table->string('kontak_person')->nullable();
            $table->string('kontak_telepon')->nullable();
            $table->string('rekening_bank')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('atas_nama')->nullable();
            $table->enum('status', ['Aktif', 'Non_Aktif'])->default('Aktif');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor');
    }
};
