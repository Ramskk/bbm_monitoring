<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BBM;
use App\Models\Stok;
use App\Models\Kendaraan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedBBMs();
        $this->seedStocks();
        $this->seedVehicles();
    }

    /**
     * Seed 3 jenis BBM: Solar, Pertalite, Pertamax.
     */
    private function seedBBMs(): void
    {
        $bbms = [
            [
                'kode'        => 'BBM-001',
                'nama'        => 'Solar',
                'jenis'       => 'Solar',
                'harga_per_liter' => 8500,
                'is_active'  => true,
                'keterangan' => 'Solar Grade A untuk mesin diesel',
            ],
            [
                'kode'        => 'BBM-002',
                'nama'        => 'Pertalite',
                'jenis'       => 'Pertalite',
                'harga_per_liter' => 11500,
                'is_active'  => true,
                'keterangan' => 'Pertalite untuk mobil umum & mobil penumpang',
            ],
            [
                'kode'        => 'BBM-003',
                'nama'        => 'Pertamax',
                'jenis'       => 'Pertamax',
                'harga_per_liter' => 15000,
                'is_active'  => true,
                'keterangan' => 'Pertamax untuk mobil kendaraan ringan',
            ],
        ];

        foreach ($bbms as $bbm) {
            BBM::create($bbm);
        }
    }

    /**
     * Buat 1 record stok per jenis BBM dengan jumlah awal: 0.
     */
    private function seedStocks(): void
    {
        DB::table('stok')->insert([
            [
                'bbm_id'        => 1,
                'jumlah'        => 0,
                'stok_minimum'  => 1000,
                'stok_maksimum' => 5000,
                'lokasi'        => 'Depot 1',
            ],
            [
                'bbm_id'        => 2,
                'jumlah'        => 0,
                'stok_minimum'  => 1500,
                'stok_maksimum' => 8000,
                'lokasi'        => 'Depot 2',
            ],
            [
                'bbm_id'        => 3,
                'jumlah'        => 0,
                'stok_minimum'  => 2000,
                'stok_maksimum' => 10000,
                'lokasi'        => 'Depot 3',
            ],
        ]);
    }

    /**
     * Seed 3 kendaraan contoh.
     *
     * Catatan: Kendaraan memerlukan bbm_id yang valid.
     * Karena kita seed BBM setelah kendaraan, kita perlu handle order seeding.
     * Solusinya: seed BBM dulu, baru kendaraan.
     */
    private function seedVehicles(): void
    {
        // Pastikan BBM sudah ada (akan dipanggil setelah seedBBMs())
        $bbm1 = BBM::where('kode', 'BBM-001')->first(); // Solar
        $bbm2 = BBM::where('kode', 'BBM-002')->first(); // Pertalite
        $bbm3 = BBM::where('kode', 'BBM-003')->first(); // Pertamax

        if (!$bbm1 || !$bbm2 || !$bbm3) {
            throw new \Exception(
                "Master data BBM belum di-seed. Pastikan RolePermissionSeeder & MasterDataSeeder::seedBBMs() sudah dijalankan."
            );
        }

        $vehicles = [
            [
                'nomor_polisi'     => 'B 1234 XYZ',
                'nama'             => 'Kamco 2.5 Turbo',
                'merek'            => 'Kamco',
                'model'            => '2.5 Turbo',
                'tahun'            => 2022,
                'jenis'            => 'Motor',
                'bbm_id'           => $bbm1->id, // Solar
                'kapasitas_tangki' => 100,
                'konsumsi_bbm_standar' => 19, // km/liter
                'odometer_awal'    => 50000,
                'odometer_terakhir' => 50000,
                'departemen'       => 'Operasional',
                'pengemudi_default' => 'Andi',
                'status'           => 'Aktif',
            ],
            [
                'nomor_polisi'     => 'B 5678 ABC',
                'nama'             => 'Toyota Hiace',
                'merek'            => 'Toyota',
                'model'            => 'Hiace',
                'tahun'            => 2021,
                'jenis'            => 'Mobil',
                'bbm_id'           => $bbm2->id, // Pertalite
                'kapasitas_tangki' => 70,
                'konsumsi_bbm_standar' => 14.5, // km/liter
                'odometer_awal'    => 45000,
                'odometer_terakhir' => 45000,
                'departemen'       => 'Operasional',
                'pengemudi_default' => 'Budi',
                'status'           => 'Aktif',
            ],
            [
                'nomor_polisi'     => 'B 9012 DEF',
                'nama'             => 'Honda Beat',
                'merek'            => 'Honda',
                'model'            => 'Beat',
                'tahun'            => 2023,
                'jenis'            => 'Motor',
                'bbm_id'           => $bbm3->id, // Pertamax
                'kapasitas_tangki' => 45,
                'konsumsi_bbm_standar' => 25, // km/liter
                'odometer_awal'    => 20000,
                'odometer_terakhir' => 20000,
                'departemen'       => 'IT',
                'pengemudi_default' => 'Candra',
                'status'           => 'Aktif',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Kendaraan::create($vehicle);
        }
    }
}
