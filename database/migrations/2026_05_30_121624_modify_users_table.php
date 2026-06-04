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
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->unique()->nullable()->after('name');
            $table->string('departemen')->nullable()->after('employee_id');
            $table->string('jabatan')->nullable()->after('departemen');
            $table->string('telepon')->nullable()->after('jabatan');
            $table->boolean('is_active')->default(true)->after('telepon');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->softDeletes()->after('last_login_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['employee_id', 'departemen', 'jabatan', 'telepon', 'is_active', 'last_login_at', 'last_login_ip']);
        });
    }
};
