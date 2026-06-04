<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\BBM;
use App\Models\Stok;
use App\Models\Kendaraan;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createRoles();
        $this->createUsers();
    }

    /**
     * Membuat 7 role sesuai spesifikasi.
     */
    private function createRoles(): void
    {
        $roles = [
            'super_admin',
            'admin',
            'kadiv',
            'petugas_operasional',
            'admin_gudang',
            'admin_pengadaan',
            'viewer',
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => $role],
                ['guard_name' => 'web']
            );
        }
    }

    /**
     * Membuat 1 user default per role dengan password 'password'.
     */
    private function createUsers(): void
    {
        $roles = [
            'super_admin'    => ['email' => 'superadmin@bbm.com', 'name' => 'Super Admin', 'departemen' => 'IT', 'jabatan' => 'Super Administrator'],
            'admin'          => ['email' => 'admin@bbm.com', 'name' => 'Administrator', 'departemen' => 'IT', 'jabatan' => 'Administrator'],
            'kadiv'          => ['email' => 'kadiv@bbm.com', 'name' => 'Kadir Divisi', 'departemen' => 'Operasional', 'jabatan' => 'Kadir Divisi Operasional'],
            'petugas_operasional' => ['email' => 'petugas@bbm.com', 'name' => 'Petugas Operasional', 'departemen' => 'Operasional', 'jabatan' => 'Petugas Operasional'],
            'admin_gudang'   => ['email' => 'gudang@bbm.com', 'name' => 'Admin Gudang', 'departemen' => 'Logistik', 'jabatan' => 'Admin Gudang'],
            'admin_pengadaan' => ['email' => 'pengadaan@bbm.com', 'name' => 'Admin Pengadaan', 'departemen' => 'Logistik', 'jabatan' => 'Admin Pengadaan'],
            'viewer'         => ['email' => 'viewer@bbm.com', 'name' => 'Viewer', 'departemen' => 'IT', 'jabatan' => 'Viewer'],
        ];

        foreach ($roles as $roleName => $userData) {
            User::firstOrCreate(
                [
                    'email' => $userData['email'],
                ],
                [
                    'name'           => $userData['name'],
                    'email'          => $userData['email'],
                    'password'       => Hash::make('password'),
                    'employee_id'    => Str::random(10),
                    'departemen'     => $userData['departemen'],
                    'jabatan'        => $userData['jabatan'],
                    'telepon'        => '081234567890',
                    'is_active'      => true,
                    'last_login_at'  => null,
                    'last_login_ip'  => null,
                ]
            );

            $user = User::where('email', $userData['email'])->first();
            if ($user) {
                $user->syncRoles([$roleName]);
            }
        }
    }
}
