<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Di Laravel 13, seeder dapat dipanggil secara manual atau
     * melalui Artisan: php artisan db:seed
     *
     * Untuk seed ulang dari awal (development only):
     *   php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            MasterDataSeeder::class,
        ]);
    }
}
