# 🛢️ Sistem Pengawasan BBM Industri
### Laravel 13 · PHP 8.3+ · Laragon 8.6.1 · MySQL 8.0 · Spatie Permission v7

> Sistem manajemen bahan bakar minyak berbasis web untuk efisiensi operasional industri.
> Mencakup pencatatan pemakaian, manajemen stok, pengadaan, approval bertingkat, dan laporan analitik.

---

## 📑 Daftar Isi

- [Versi yang Digunakan](#-versi-yang-digunakan)
- [Gambaran Sistem](#-gambaran-sistem)
- [Stack Teknologi](#-stack-teknologi)
- [Struktur Direktori Lengkap](#-struktur-direktori-lengkap)
- [Role & Hak Akses](#-role--hak-akses)
- [Checklist Pembuatan](#-checklist-pembuatan)
- [Panduan Instalasi Laragon 8.6.1](#-panduan-instalasi-laragon-861)
- [Panduan Instalasi Project](#-panduan-instalasi-project)
- [Konfigurasi Environment](#-konfigurasi-environment)
- [Skema Database](#-skema-database)
- [Alur Kerja Sistem](#-alur-kerja-sistem)
- [Routes Lengkap](#-routes-lengkap)
- [Fitur Baru Laravel 13 yang Dipakai](#-fitur-baru-laravel-13-yang-dipakai)
- [Kredensial Default](#-kredensial-default)
- [Keamanan Sistem](#-keamanan-sistem)

---

## 📦 Versi yang Digunakan

| Komponen | Versi | Tanggal Rilis | Catatan |
|---|---|---|---|
| **Laragon** | **8.6.1** | 14 April 2026 | Local dev environment untuk Windows |
| **Laravel** | **13.8.x** | 27 Mei 2026 (terbaru) | Rilis besar 17 Maret 2026 |
| **PHP** | **8.3+** | — | Minimum requirement Laravel 13 |
| **MySQL** | **8.0+** | — | Sudah bundled di Laragon |
| **Composer** | **2.8+** | — | Bundled di Laragon |
| **Spatie Permission** | **7.4.1** | 29 April 2026 | Kompatibel Laravel 12 & 13 |
| **Maatwebsite Excel** | **3.1+** | — | Export Excel |
| **DomPDF** | **3.x** | — | Export PDF |
| **Bootstrap** | **5.3** | — | Frontend via CDN |
| **Chart.js** | **4.x** | — | Grafik via CDN |

> ⚠️ **Penting**: Laravel 13 membutuhkan **PHP 8.3 minimum**. Pastikan Laragon dikonfigurasi ke PHP 8.3+
> sebelum memulai. PHP 8.2 ke bawah **tidak kompatibel** dengan Laravel 13.

---

## 🧭 Gambaran Sistem

Sistem ini terdiri dari **3 inti utama** yang saling terhubung:

```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│   OPERASIONAL BBM          MANAJEMEN STOK               │
│   ──────────────           ──────────────               │
│   • Input pemakaian        • Stok masuk / keluar        │
│   • Monitoring odometer    • Kartu stok                 │
│   • Kalkulasi efisiensi    • Batas minimum & maksimum   │
│   • Approval transaksi     • Histori mutasi stok        │
│                                                         │
│              ↕ Terhubung lewat ↕                        │
│        Login · Role · Approval · Stok Transaksi         │
│                                                         │
│   MONITORING & EVALUASI                                 │
│   ──────────────────────                                │
│   • Laporan efisiensi km/liter per kendaraan            │
│   • Laporan biaya per bulan / departemen                │
│   • Deteksi anomali (kendaraan boros)                   │
│   • Audit log seluruh aktivitas user                    │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 🧰 Stack Teknologi

| Layer | Teknologi | Versi |
|---|---|---|
| Framework | Laravel | ^13.0 |
| PHP | PHP | ^8.3 |
| Database | MySQL | 8.0+ |
| Auth | Laravel Breeze (Blade) | latest |
| Role & Permission | Spatie Laravel Permission | ^7.0 |
| Export Excel | Maatwebsite Excel | ^3.1 |
| Export PDF | Barryvdh DomPDF | ^3.0 |
| Frontend CSS | Bootstrap | 5.3 (CDN) |
| Chart | Chart.js | 4.x (CDN) |
| Icons | Bootstrap Icons | 1.11+ (CDN) |
| Dev Environment | Laragon | 8.6.1 |

---

## 📁 Struktur Direktori Lengkap

```
sistem-bbm/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php              ← Login & Logout + rate limiting
│   │   │   ├── DashboardController.php         ← Dashboard utama + summary
│   │   │   ├── OperationalController.php       ← Input & riwayat pemakaian BBM
│   │   │   ├── GudangController.php            ← Manajemen stok & kartu stok
│   │   │   ├── POController.php                ← Purchase Order pengadaan
│   │   │   ├── VendorController.php            ← Master data vendor/supplier
│   │   │   ├── ApprovalController.php          ← Proses approve/reject
│   │   │   ├── ReportController.php            ← Laporan & export
│   │   │   ├── UserController.php              ← Manajemen user & role
│   │   │   ├── KendaraanController.php         ← Master data kendaraan
│   │   │   └── BBMController.php               ← Master jenis BBM
│   │   │
│   │   ├── Middleware/
│   │   │   └── AuditLogMiddleware.php          ← Catat semua akses endpoint sensitif
│   │   │
│   │   └── Requests/                           ← Form Request (validasi input)
│   │       ├── StorePemakaianRequest.php       ← Validasi pemakaian BBM
│   │       ├── StorePORequest.php              ← Validasi Purchase Order
│   │       ├── StoreStokMasukRequest.php       ← Validasi stok masuk
│   │       ├── StoreKendaraanRequest.php       ← Validasi data kendaraan
│   │       ├── StoreBBMRequest.php             ← Validasi jenis BBM
│   │       ├── StoreVendorRequest.php          ← Validasi data vendor
│   │       ├── StoreUserRequest.php            ← Validasi user + password rules
│   │       └── ApprovalRequest.php             ← Validasi keputusan approval
│   │
│   ├── Models/
│   │   ├── User.php                            ← HasRoles, softDelete, lastLogin
│   │   ├── BBM.php                             ← generateKode(), isStokKritis()
│   │   ├── Kendaraan.php                       ← updateOdometer(), rataEfisiensi
│   │   ├── Stok.php                            ← isKritis(), getPersentase()
│   │   ├── MutasiStok.php                      ← Histori setiap perubahan stok
│   │   ├── TransaksiBBM.php                    ← generateNoTransaksi(), scopes
│   │   ├── Vendor.php                          ← generateKode()
│   │   ├── PO.php                              ← canTransitionTo(), generateNoPO()
│   │   ├── Approval.php                        ← Polymorphic morphTo
│   │   └── AuditLog.php                        ← Log semua aktivitas user
│   │
│   └── Services/
│       ├── StockService.php                    ← lockForUpdate, rollback stok
│       ├── ApprovalService.php                 ← Approval + role check backend
│       ├── ReportService.php                   ← Efisiensi, biaya, anomali
│       └── AuditLogService.php                 ← Helper pencatatan log
│
├── database/
│   ├── migrations/
│   │   ├── ..._modify_users_table.php          ← Tambah kolom kustom ke users
│   │   ├── ..._create_bbm_table.php
│   │   ├── ..._create_kendaraan_table.php
│   │   ├── ..._create_stok_table.php
│   │   ├── ..._create_mutasi_stok_table.php
│   │   ├── ..._create_transaksi_bbm_table.php
│   │   ├── ..._create_vendor_table.php
│   │   ├── ..._create_po_table.php
│   │   ├── ..._create_approval_table.php
│   │   └── ..._create_audit_logs_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php                  ← Entry point semua seeder
│       ├── RolePermissionSeeder.php            ← 7 role + user default
│       └── MasterDataSeeder.php                ← BBM, stok, kendaraan contoh
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php                   ← Layout utama Bootstrap 5
│       │   └── partials/
│       │       ├── sidebar.blade.php           ← Sidebar menu per role
│       │       └── navbar.blade.php            ← Navbar + notifikasi stok kritis
│       │
│       ├── auth/
│       │   └── login.blade.php
│       │
│       ├── dashboard/
│       │   └── index.blade.php                 ← Widget, grafik 7 hari, kendaraan boros
│       │
│       ├── operational/
│       │   ├── index.blade.php                 ← Tabel transaksi + filter
│       │   ├── create.blade.php                ← Form input + AJAX info kendaraan
│       │   ├── show.blade.php                  ← Detail + status approval
│       │   └── monitoring_odometer.blade.php
│       │
│       ├── gudang/
│       │   ├── index.blade.php                 ← Card stok + progress bar
│       │   ├── stok_masuk.blade.php
│       │   └── kartu_stok.blade.php
│       │
│       ├── po/
│       │   ├── index.blade.php
│       │   ├── create.blade.php                ← Form PO + auto-hitung total
│       │   └── show.blade.php                  ← Detail + tombol ubah status
│       │
│       ├── approval/
│       │   ├── index.blade.php                 ← Tab: transaksi pending & PO pending
│       │   └── riwayat.blade.php
│       │
│       ├── laporan/
│       │   ├── efisiensi.blade.php             ← Tabel + grafik bar per kendaraan
│       │   ├── biaya.blade.php                 ← Grafik biaya per bulan
│       │   └── anomali.blade.php               ← Kendaraan di bawah standar
│       │
│       ├── kendaraan/
│       │   ├── index.blade.php
│       │   ├── form.blade.php
│       │   └── show.blade.php                  ← Detail + riwayat pengisian
│       │
│       ├── bbm/
│       │   ├── index.blade.php
│       │   └── form.blade.php
│       │
│       ├── vendor/
│       │   ├── index.blade.php
│       │   └── form.blade.php
│       │
│       └── user/
│           ├── index.blade.php
│           └── form.blade.php
│
├── routes/
│   └── web.php                                 ← Semua route aplikasi
│
├── bootstrap/
│   └── app.php                                 ← Middleware alias, routing config
│
├── config/
│   ├── permission.php                          ← Config Spatie (publish dulu)
│   └── excel.php                              ← Config Maatwebsite
│
└── .env                                        ← Konfigurasi environment
```

---

## 👥 Role & Hak Akses

| Role | Dashboard | Operasional | Gudang | Pengadaan | Approval | Laporan | Pengaturan |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `super_admin` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `admin` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ |
| `kadiv` | ✅ | 👁️ | 👁️ | 👁️ | ✅ | ✅ | ❌ |
| `petugas_operasional` | ✅ | ✅ | ❌ | ❌ | ❌ | 👁️ | ❌ |
| `admin_gudang` | ✅ | 👁️ | ✅ | ❌ | ❌ | 👁️ | ❌ |
| `admin_pengadaan` | ✅ | ❌ | 👁️ | ✅ | ❌ | 👁️ | ❌ |
| `viewer` | ✅ | 👁️ | 👁️ | 👁️ | ❌ | ✅ | ❌ |

> ✅ Akses penuh · 👁️ Hanya lihat · ⚠️ Terbatas · ❌ Tidak bisa akses

---

## ✅ Checklist Pembuatan

### 🖥️ TAHAP 0 — Setup Laragon 8.6.1

- [ ] Download Laragon 8.6.1 dari https://laragon.org/download
- [ ] Install Laragon (pilih lokasi, misal `C:\laragon`)
- [ ] Buka Laragon → klik kanan tray icon → PHP → pilih **PHP 8.3.x**
- [ ] Verifikasi PHP: buka Laragon Terminal → `php -v` (harus muncul 8.3.x)
- [ ] Verifikasi Composer: `composer -V`
- [ ] Verifikasi MySQL aktif di Laragon (port 3306)
- [ ] Buat database baru: Laragon → Database (HeidiSQL/phpMyAdmin) → buat `sistem_bbm`
- [ ] Pastikan Nginx/Apache aktif (port 80)

---

### 🔧 TAHAP 1 — Instalasi Project Laravel 13

- [ ] Buka Laragon Terminal di folder `C:\laragon\www`
- [ ] Buat project:
  ```bash
  composer create-project laravel/laravel:^13.0 sistem-bbm
  cd sistem-bbm
  ```
- [ ] Verifikasi versi Laravel:
  ```bash
  php artisan --version
  # Harus: Laravel Framework 13.x.x
  ```
- [ ] Install semua dependency:
  ```bash
  composer require spatie/laravel-permission:"^7.0"
  composer require laravel/breeze
  composer require maatwebsite/excel
  composer require barryvdh/laravel-dompdf
  ```
- [ ] Setup Breeze (Blade, tanpa Vue/React):
  ```bash
  php artisan breeze:install blade
  npm install
  npm run build
  ```
- [ ] Publish config Spatie Permission:
  ```bash
  php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
  ```
- [ ] Publish config Excel:
  ```bash
  php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
  ```
- [ ] Publish config DomPDF:
  ```bash
  php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
  ```
- [ ] Generate app key:
  ```bash
  php artisan key:generate
  ```

---

### ⚙️ TAHAP 2 — Konfigurasi bootstrap/app.php

> Laravel 13 tetap menggunakan `bootstrap/app.php` untuk registrasi middleware.
> File `app/Http/Kernel.php` sudah dihapus sejak Laravel 11.

- [ ] Buka `bootstrap/app.php`, tambahkan alias middleware:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->alias([
          'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
          'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
          'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
          'audit'              => \App\Http\Middleware\AuditLogMiddleware::class,
      ]);
  })
  ```

---

### 🗄️ TAHAP 3 — Database & Migration

Buat file-file migration di `database/migrations/` dengan urutan timestamp yang benar:

- [ ] `..._modify_users_table.php`
  - Tambah: `employee_id`, `departemen`, `jabatan`, `telepon`, `is_active`, `last_login_at`, `last_login_ip`, `deleted_at`
- [ ] `..._create_bbm_table.php`
  - Kolom: `kode` (unique), `nama`, `jenis` (enum), `harga_per_liter`, `is_active`, `keterangan`, `deleted_at`
- [ ] `..._create_kendaraan_table.php`
  - Kolom: `nomor_polisi` (unique), `nama`, `merek`, `model`, `tahun`, `jenis` (enum), `bbm_id` (FK), `kapasitas_tangki`, `konsumsi_bbm_standar`, `odometer_awal`, `odometer_terakhir`, `departemen`, `pengemudi_default`, `status` (enum), `deleted_at`
  - Index: `[status, is_active]`, `departemen`
- [ ] `..._create_stok_table.php`
  - Kolom: `bbm_id` (unique FK), `jumlah`, `stok_minimum`, `stok_maksimum`, `lokasi`
  - Note: 1 jenis BBM = 1 record stok (unique constraint di `bbm_id`)
- [ ] `..._create_mutasi_stok_table.php`
  - Kolom: `stok_id` (FK), `bbm_id` (FK), `jenis` (enum: masuk/keluar), `jumlah`, `stok_sebelum`, `stok_sesudah`, `harga_per_liter`, `referensi_no`, `referensi_type`, `referensi_id`, `user_id` (FK), `keterangan`, `tanggal`
  - Index: `[stok_id, jenis]`, `tanggal`, `[referensi_type, referensi_id]`
- [ ] `..._create_transaksi_bbm_table.php`
  - Kolom: `no_transaksi` (unique), `kendaraan_id` (FK), `bbm_id` (FK), `stok_id` (FK), `user_id` (FK), `jumlah_liter`, `harga_per_liter`, `total_biaya`, `odometer_sebelum`, `odometer_sesudah`, `jarak_tempuh`, `efisiensi`, `status` (enum: pending/approved/rejected), `approved_by` (FK nullable), `approved_at`, `alasan_reject`, `catatan`, `tanggal_pemakaian`, `lokasi_pengisian`, `deleted_at`
  - Index: `[kendaraan_id, tanggal_pemakaian]`, `status`, `tanggal_pemakaian`
- [ ] `..._create_vendor_table.php`
  - Kolom: `kode` (unique), `nama`, `alamat`, `kota`, `telepon`, `email`, `npwp`, `kontak_person`, `kontak_telepon`, `rekening_bank`, `nama_bank`, `atas_nama`, `status` (enum), `deleted_at`
- [ ] `..._create_po_table.php`
  - Kolom: `no_po` (unique), `vendor_id` (FK), `bbm_id` (FK), `jumlah_liter`, `harga_per_liter`, `total_nilai`, `jumlah_diterima`, `tanggal_po`, `tanggal_kirim_rencana`, `tanggal_kirim_aktual`, `tanggal_terima`, `status` (enum: draft/approved/dikirim/diterima/closed/rejected), `created_by` (FK), `approved_by` (FK nullable), `approved_at`, `received_by` (FK nullable), `alasan_reject`, `deleted_at`
- [ ] `..._create_approval_table.php`
  - Kolom: `approvable_type`, `approvable_id` (polymorphic), `requested_by` (FK), `approved_by` (FK nullable), `status` (enum), `urutan`, `catatan_peminta`, `catatan_approver`, `processed_at`
- [ ] `..._create_audit_logs_table.php`
  - Kolom: `user_id` (FK nullable), `action`, `model`, `model_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, `url`, `method`, `keterangan`
  - Index: `[user_id, created_at]`, `[model, model_id]`, `[action, created_at]`
- [ ] Jalankan migrasi:
  ```bash
  php artisan migrate
  ```

---

### 🌱 TAHAP 4 — Seeders

- [ ] `RolePermissionSeeder` — buat 7 role:
  ```
  super_admin, admin, kadiv,
  petugas_operasional, admin_gudang, admin_pengadaan, viewer
  ```
- [ ] `RolePermissionSeeder` — buat 1 user default per role (password: `password`)
- [ ] `MasterDataSeeder` — seed 3 jenis BBM: Solar, Pertalite, Pertamax
- [ ] `MasterDataSeeder` — buat 1 record `stok` per jenis BBM (jumlah awal: 0)
- [ ] `MasterDataSeeder` — seed 3-5 kendaraan contoh
- [ ] Jalankan seeder:
  ```bash
  php artisan db:seed
  ```

---

### 🧩 TAHAP 5 — Models

> Laravel 13 mendukung **PHP Attributes** sebagai alternatif class properties.
> Keduanya bisa dipakai, pilih yang konsisten untuk timmu.

- [ ] `User.php`
  - Trait: `HasFactory`, `Notifiable`, `HasRoles`, `SoftDeletes`
  - Cast: `password` → `hashed`, `is_active` → `boolean`, `last_login_at` → `datetime`
  - Relasi: `transaksi()`, `approvals()`, `auditLogs()`, `posBuatan()`, `mutasiStok()`
  - Scope: `scopeActive()`
  - Method: `updateLastLogin(string $ip)`

- [ ] `BBM.php`
  - Trait: `SoftDeletes`
  - Relasi: `stok()` (hasOne), `kendaraan()`, `transaksi()`, `po()`, `mutasiStok()`
  - Scope: `scopeActive()`
  - Accessor: `jenis_label`, `stok_tersedia`
  - Static method: `generateKode()` → format `BBM-001`
  - Method: `isStokKritis()`

- [ ] `Kendaraan.php`
  - Trait: `SoftDeletes`
  - Relasi: `bbm()` (belongsTo), `transaksi()` (hasMany)
  - Scope: `scopeAktif()`, `scopeByDepartemen()`
  - Accessor: `status_label`, `status_badge`, `jenis_label`, `rata_efisiensi`, `total_biaya_bulan_ini`
  - Method: `updateOdometer(float $odometer)`

- [ ] `Stok.php`
  - Relasi: `bbm()`, `mutasi()`
  - Method: `isKritis()`, `isPenuh()`
  - Accessor: `persentase`, `status_warn`

- [ ] `MutasiStok.php`
  - Relasi: `stok()`, `bbm()`, `user()`
  - Accessor: `jenis_label`, `jenis_badge`, `nilai_mutasi`

- [ ] `TransaksiBBM.php`
  - Trait: `SoftDeletes`
  - Relasi: `kendaraan()`, `bbm()`, `stok()`, `user()`, `approvedBy()`, `approval()`
  - Scope: `scopePending()`, `scopeApproved()`, `scopeBulanIni()`, `scopeByKendaraan()`
  - Accessor: `status_label`, `status_badge`
  - Method: `isApproved()`, `isPending()`, `isRejected()`
  - Static method: `generateNoTransaksi()` → format `TRX-YYYYMMDD-0001`

- [ ] `Vendor.php`
  - Trait: `SoftDeletes`
  - Relasi: `po()` (hasMany)
  - Scope: `scopeAktif()`
  - Accessor: `status_badge`
  - Static method: `generateKode()` → format `VND-001`

- [ ] `PO.php`
  - Trait: `SoftDeletes`
  - Relasi: `vendor()`, `bbm()`, `pembuat()`, `penyetuju()`, `penerima()`, `approval()`
  - Scope: `scopeByStatus()`
  - Accessor: `status_badge`, `sisa_kiriman`
  - Const: `STATUS_ORDER = ['draft','approved','dikirim','diterima','closed']`
  - Method: `canTransitionTo(string $newStatus)` — validasi lifecycle status
  - Static method: `generateNoPO()` → format `PO-YYYYMM-0001`

- [ ] `Approval.php`
  - Relasi: `approvable()` (morphTo), `peminta()`, `approver()`
  - Scope: `scopePending()`
  - Method: `isPending()`, `isApproved()`, `isRejected()`

- [ ] `AuditLog.php`
  - Relasi: `user()`
  - Accessor: `action_label`, `action_badge`

---

### ⚙️ TAHAP 6 — Services

- [ ] `AuditLogService.php`
  - Method static `log(action, model, modelId, oldValues, newValues, keterangan)`
  - Method static `getDiff(array $old, array $new)` → kembalikan diff perubahan

- [ ] `StockService.php`
  - Method `kurangiStok(bbmId, jumlahLiter, referensiNo, referensiType, referensiId, keterangan)`
    - Gunakan `Stok::lockForUpdate()->where('bbm_id', $id)->firstOrFail()`
    - Lempar exception jika stok kurang
    - Buat record `MutasiStok` jenis `keluar`
  - Method `tambahStok(bbmId, jumlahLiter, hargaPerLiter, referensiNo, referensiType, referensiId)`
    - Cek kapasitas maksimum sebelum tambah
    - Update `harga_per_liter` BBM jika berubah
    - Buat record `MutasiStok` jenis `masuk`
  - Method `rollbackKeluar(int $mutasiStokId)` — kembalikan stok saat transaksi ditolak
  - Method `cekStokCukup(int $bbmId, float $jumlahLiter): bool`
  - Method `getStokKritis()` — return stok di bawah minimum

- [ ] `ApprovalService.php`
  - Method `prosesTransaksi(transaksiId, keputusan, catatan)`
    - **Security**: cek role di backend (`hasAnyRole(['kadiv','admin','super_admin'])`)
    - Jika `approved`: update status transaksi
    - Jika `rejected`: rollback stok via `StockService::rollbackKeluar()`
    - Update record `Approval`
    - Catat ke audit log
  - Method `prosesPO(poId, keputusan, catatan)`
    - **Security**: cek role di backend
    - Validasi status harus `draft` sebelum diproses
    - Update status PO + `approved_by`, `approved_at`

- [ ] `ReportService.php`
  - Method `efisiensiPerKendaraan(Carbon $dari, Carbon $sampai, string $departemen = null)`
    - Query JOIN kendaraan + BBM + transaksi
    - Hitung `rata_efisiensi`, `deviasi_efisiensi`, `status_efisiensi`
  - Method `biayaPerBulan(int $tahun)`
    - Group by bulan + departemen + jenis BBM
  - Method `deteksiAnomali(Carbon $dari, Carbon $sampai, float $threshold = 0.7)`
    - Filter kendaraan dengan `efisiensi < konsumsi_standar * threshold`
  - Method `dashboardSummary()` → array untuk widget dashboard

---

### 🛡️ TAHAP 7 — Middleware & Form Requests

- [ ] `AuditLogMiddleware.php`
  - Log semua request non-GET ke endpoint sensitif
  - Hanya log jika `auth()->check()`

- [ ] `StorePemakaianRequest.php`
  - `authorize()`: cek role `petugas_operasional|admin|super_admin`
  - Rules: `kendaraan_id`, `jumlah_liter` (min:0.1), `odometer_sesudah`, `tanggal_pemakaian` (before_or_equal:today)
  - `withValidator()`: cek odometer tidak turun, jumlah liter ≤ kapasitas tangki, kendaraan aktif

- [ ] `StorePORequest.php`
  - `authorize()`: cek role `admin_pengadaan|admin|super_admin`
  - Rules: `vendor_id`, `bbm_id`, `jumlah_liter` (min:1), `harga_per_liter` (min:1), `tanggal_po`, `tanggal_kirim_rencana` (after_or_equal:tanggal_po)
  - `prepareForValidation()`: auto-hitung `total_nilai`

- [ ] `StoreStokMasukRequest.php`
  - `authorize()`: cek role `admin_gudang|admin|super_admin`

- [ ] `StoreKendaraanRequest.php`
  - `Rule::unique('kendaraan','nomor_polisi')->ignore($id)->whereNull('deleted_at')`

- [ ] `StoreBBMRequest.php`
  - Validasi `jenis` dari enum list

- [ ] `StoreVendorRequest.php`
  - Validasi `email` unique dengan ignore soft-delete

- [ ] `StoreUserRequest.php`
  - Password: `Password::min(8)->letters()->numbers()` (opsional saat update)
  - Validasi `role` harus ada di tabel `roles`

- [ ] `ApprovalRequest.php`
  - **Security**: `keputusan` hanya boleh `approved` atau `rejected` — tidak boleh value bebas dari frontend
  - `catatan` required jika `keputusan === 'rejected'`

---

### 🎮 TAHAP 8 — Controllers

- [ ] `AuthController.php`
  - `showLogin()`: redirect ke dashboard jika sudah login
  - `login()`: rate limiting 5x/menit per IP, cek `is_active`, update `last_login`, audit log
  - `logout()`: invalidate session, audit log

- [ ] `DashboardController.php`
  - Ambil: `$summary` (dari ReportService), `$stokBBM`, `$transaksiTerbaru`, `$kendaraanBoros`, `$grafikHarian`, `$poPending`

- [ ] `OperationalController.php`
  - `index()`: daftar transaksi dengan filter (status, kendaraan, tanggal); non-admin hanya lihat transaksi sendiri
  - `create()`: form input + data kendaraan & stok
  - `store()`: jalankan di `DB::transaction()`, kurangi stok via service, buat record Approval
  - `show()`: detail transaksi
  - `monitoringOdometer()`: tabel odometer semua kendaraan aktif
  - `getKendaraanInfo()`: AJAX endpoint untuk form

- [ ] `GudangController.php`
  - `index()`: stok per BBM + stok kritis + mutasi terbaru
  - `kartuStok()`: histori mutasi dengan filter bbm_id + tanggal
  - `formStokMasuk()`: form stok masuk (bisa dari PO atau manual)
  - `stokMasuk()`: proses via StockService, update PO jika dari PO
  - `updateBatasStok()`: update `stok_minimum`, `stok_maksimum`, `lokasi`

- [ ] `POController.php`
  - `index()`, `create()`, `store()`: CRUD PO + buat record Approval
  - `show()`: detail PO
  - `updateStatus()`: validasi `canTransitionTo()` sebelum update
  - `close()`: PO ke status `closed`

- [ ] `ApprovalController.php`
  - `index()`: tab transaksi pending + PO pending (hanya kadiv/admin)
  - `proses()`: delegasikan ke `ApprovalService` (tidak proses langsung di controller)
  - `riwayat()`: riwayat approval yang sudah diproses

- [ ] `ReportController.php`
  - `efisiensi()`, `biaya()`, `anomali()`: delegasikan ke `ReportService`
  - `exportEfisiensi()`: catat audit log + return export

- [ ] `UserController.php`
  - CRUD user, `toggleStatus()`, assign role via `syncRoles()`
  - Tidak bisa hapus/nonaktifkan akun sendiri

- [ ] `KendaraanController.php`
  - CRUD kendaraan + `show()` dengan riwayat transaksi
  - Tidak bisa hapus jika ada transaksi pending

- [ ] `BBMController.php`
  - CRUD jenis BBM + auto-create record `Stok` saat BBM baru

- [ ] `VendorController.php`
  - CRUD vendor + cek PO aktif sebelum hapus

---

### 🛣️ TAHAP 9 — Routes

- [ ] Buat file `routes/web.php` dengan struktur:
  - Route `login` + `logout` (tanpa auth middleware)
  - Route group `middleware(['auth'])` untuk semua halaman terproteksi
  - `Route::resource('kendaraan', KendaraanController::class)`
  - `Route::resource('bbm', BBMController::class)`
  - `Route::resource('vendor', VendorController::class)`
  - `Route::resource('po', POController::class)` + route tambahan `updateStatus`, `close`
  - Route manual untuk `operational`, `gudang`, `approval`, `laporan`, `user`
  - Route AJAX `operational/kendaraan/{id}/info`

---

### 🎨 TAHAP 10 — Views (Blade)

- [ ] `layouts/app.blade.php` — layout Bootstrap 5, CDN Chart.js, CDN Bootstrap Icons
- [ ] `layouts/partials/sidebar.blade.php` — menu per role dengan `@role('...')`
- [ ] `layouts/partials/navbar.blade.php` — info user + badge stok kritis
- [ ] `auth/login.blade.php`
- [ ] `dashboard/index.blade.php` — widget summary, grafik 7 hari (Chart.js), top 5 kendaraan boros
- [ ] `operational/index.blade.php` — tabel + filter + pagination
- [ ] `operational/create.blade.php` — form dengan AJAX (`fetch`) info kendaraan
- [ ] `operational/show.blade.php` — detail + timeline status approval
- [ ] `operational/monitoring_odometer.blade.php`
- [ ] `gudang/index.blade.php` — card stok per BBM + progress bar + badge kritis
- [ ] `gudang/stok_masuk.blade.php`
- [ ] `gudang/kartu_stok.blade.php`
- [ ] `po/index.blade.php` — badge status lifecycle
- [ ] `po/create.blade.php` — JS auto-hitung total nilai
- [ ] `po/show.blade.php` — detail + tombol ubah status
- [ ] `approval/index.blade.php` — 2 tab: transaksi pending & PO pending
- [ ] `approval/riwayat.blade.php`
- [ ] `laporan/efisiensi.blade.php` — tabel + grafik bar + badge status efisiensi
- [ ] `laporan/biaya.blade.php` — grafik line biaya per bulan
- [ ] `laporan/anomali.blade.php` — tabel kendaraan boros highlight merah
- [ ] `kendaraan/index.blade.php`, `form.blade.php`, `show.blade.php`
- [ ] `bbm/index.blade.php`, `form.blade.php`
- [ ] `vendor/index.blade.php`, `form.blade.php`
- [ ] `user/index.blade.php`, `form.blade.php`

---

### 🔐 TAHAP 11 — Keamanan & Hardening

- [ ] Rate limiting login: 5 percobaan per menit per IP (`RateLimiter`)
- [ ] Validasi odometer tidak bisa turun (di Form Request, bukan di controller)
- [ ] `lockForUpdate()` di semua operasi yang baca + tulis stok bersamaan
- [ ] `DB::transaction()` di semua operasi yang mencakup lebih dari 1 tabel
- [ ] Keputusan approval **hanya** diproses di backend via `ApprovalService` (bukan dari nilai hidden form)
- [ ] `canTransitionTo()` di model PO: PO tidak bisa lompat status
- [ ] Soft delete aktif: `User`, `BBM`, `Kendaraan`, `Vendor`, `PO`, `TransaksiBBM`
- [ ] Password cast `hashed` di model User (fitur bawaan Laravel, tidak perlu `Hash::make()` manual)
- [ ] `$fillable` terdefinisi di semua model
- [ ] Eloquent ORM dipakai di semua query (tidak ada raw query tanpa binding)
- [ ] CSRF protection aktif di semua form (`@csrf`)
- [ ] Spatie middleware `role` + `permission` dipakai di route dan `authorize()` di Form Request
- [ ] Audit log tercatat: create, update, delete, approve, reject, login, logout, export
- [ ] Cek `is_active` saat login — akun nonaktif langsung logout

---

### 🧪 TAHAP 12 — Testing & Finalisasi

- [ ] Test login semua 7 role, pastikan redirect dan menu sidebar sesuai
- [ ] Test input pemakaian: stok berkurang, odometer kendaraan terupdate
- [ ] Test approval transaksi: stok dikembalikan jika ditolak
- [ ] Test PO lifecycle: `draft → approved → dikirim → diterima → closed`
- [ ] Test stok masuk dari PO: `jumlah_diterima` bertambah di record PO
- [ ] Test grafik dashboard tampil dengan data benar
- [ ] Test laporan efisiensi + anomali
- [ ] Test rate limiting: coba login gagal 6 kali berturut-turut
- [ ] Test soft delete: data terhapus tidak muncul di list
- [ ] Test kendaraan dengan status `non_aktif` tidak bisa diisi BBM
- [ ] Test PO dengan status `closed` tidak bisa diubah statusnya
- [ ] Test user dengan `is_active = false` tidak bisa login
- [ ] Reset dan re-seed untuk memastikan seeder berjalan bersih:
  ```bash
  php artisan migrate:fresh --seed
  ```

---

## 💻 Panduan Instalasi Laragon 8.6.1

```
1. Download: https://laragon.org/download
   Pilih: Laragon Full (8.6.1) — sudah bundled PHP 8.3, MySQL, Nginx, Composer

2. Install ke C:\laragon (hindari path dengan spasi)

3. Buka Laragon → klik icon tray → PHP → Switch PHP → pilih 8.3.x
   (Jika belum ada, Laragon bisa auto-download via Menu > Tools > Quick Add > PHP 8.3)

4. Verifikasi di Laragon Terminal:
   php -v          → PHP 8.3.x
   composer -V     → Composer 2.x
   mysql --version → MySQL 8.x

5. Start All Services (Nginx + MySQL)

6. Buat database:
   Menu > Database → klik HeidiSQL atau phpMyAdmin
   Buat database baru: sistem_bbm
   Charset: utf8mb4 | Collation: utf8mb4_unicode_ci

7. Folder project ada di: C:\laragon\www\
   Akses via: http://sistem-bbm.test (Laragon auto-buat virtual host)
```

---

## 🚀 Panduan Instalasi Project

```bash
# 1. Masuk ke folder www Laragon
cd C:\laragon\www

# 2. Buat project Laravel 13
composer create-project laravel/laravel:^13.0 sistem-bbm
cd sistem-bbm

# 3. Install semua dependency
composer require spatie/laravel-permission:"^7.0"
composer require laravel/breeze
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf

# 4. Setup Breeze
php artisan breeze:install blade
npm install && npm run build

# 5. Publish config
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"

# 6. Generate key
php artisan key:generate

# 7. Migrasi + seeder
php artisan migrate
php artisan db:seed

# 8. Jalankan server (atau akses via http://sistem-bbm.test di Laragon)
php artisan serve
```

---

## ⚙️ Konfigurasi Environment

```env
APP_NAME="Sistem Pengawasan BBM"
APP_ENV=local
APP_KEY=                            # auto di-generate
APP_DEBUG=true
APP_URL=http://sistem-bbm.test      # atau http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_bbm
DB_USERNAME=root
DB_PASSWORD=                        # Laragon default: kosong

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database

# (Opsional) untuk notifikasi email
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

---

## 🗃️ Skema Database

```
users                    bbm                      kendaraan
──────────────────       ──────────────────       ──────────────────────
id                       id                       id
employee_id (unique)     kode (unique)            nomor_polisi (unique)
name                     nama                     nama
email (unique)           jenis (enum)             merek / model / tahun
password (hashed)        harga_per_liter          jenis (enum)
departemen               is_active                bbm_id (FK → bbm)
jabatan                  keterangan               kapasitas_tangki
telepon                  deleted_at               konsumsi_bbm_standar
is_active                                         odometer_awal
last_login_at                                     odometer_terakhir
last_login_ip            stok                     departemen
deleted_at               ──────────────────       status (enum)
                         id                       deleted_at
                         bbm_id (unique FK)
                         jumlah
                         stok_minimum
                         stok_maksimum
                         lokasi

mutasi_stok                              transaksi_bbm
──────────────────                       ──────────────────────────
id                                       id
stok_id (FK → stok)                      no_transaksi (unique)
bbm_id (FK → bbm)                        kendaraan_id (FK)
jenis (masuk/keluar)                     bbm_id (FK)
jumlah                                   stok_id (FK)
stok_sebelum                             user_id (FK)
stok_sesudah                             jumlah_liter
harga_per_liter                          harga_per_liter
referensi_no                             total_biaya
referensi_type                           odometer_sebelum
referensi_id                             odometer_sesudah
user_id (FK → users)                     jarak_tempuh
keterangan                               efisiensi (km/liter)
tanggal                                  status (pending/approved/rejected)
                                         approved_by (FK nullable)
vendor                  po               approved_at
──────────────────       ──────────────── alasan_reject
id                       id              tanggal_pemakaian
kode (unique)            no_po (unique)  deleted_at
nama                     vendor_id (FK)
alamat / kota            bbm_id (FK)
telepon / email          jumlah_liter    approval (polymorphic)
npwp                     harga_per_liter ──────────────────────
kontak_person            total_nilai     id
rekening_bank            jumlah_diterima approvable_type
status (enum)            status (enum)   approvable_id
deleted_at               created_by (FK) requested_by (FK)
                         approved_by     approved_by (FK null)
                         received_by     status (enum)
                         deleted_at      processed_at

audit_logs
──────────────────────────────────
id
user_id (FK nullable)
action (create/update/delete/approve/reject/login/logout/export)
model / model_id
old_values (JSON)
new_values (JSON)
ip_address / user_agent / url / method
keterangan
created_at
```

---

## 🔄 Alur Kerja Sistem

### Alur Pemakaian BBM
```
Petugas isi form pemakaian
        │
        ▼
StorePemakaianRequest (Form Request — Laravel 13)
  • odometer tidak turun?         ✅ / throw ValidationException
  • kendaraan aktif?              ✅ / throw ValidationException
  • stok cukup?                   ✅ / error flash
        │
        ▼
OperationalController::store()  ← DB::transaction()
        │
        ├─→ Buat TransaksiBBM (status: pending)
        ├─→ StockService::kurangiStok() ← lockForUpdate()
        ├─→ Kendaraan::updateOdometer()
        ├─→ Buat Approval (status: pending)
        └─→ AuditLogService::log('create')
        │
        ▼
Kadiv/Admin buka halaman Approval
        │
        ▼
ApprovalController::proses() → ApprovalRequest (validasi backend)
        │
        ├─→ [Setuju]  ApprovalService::prosesTransaksi('approved')
        │             TransaksiBBM.status → approved
        │             Approval.status → approved
        │             AuditLogService::log('approve')
        │
        └─→ [Tolak]   ApprovalService::prosesTransaksi('rejected')
                      StockService::rollbackKeluar() ← kembalikan stok
                      TransaksiBBM.status → rejected
                      AuditLogService::log('reject')
```

### Alur Pengadaan BBM (PO)
```
Admin Pengadaan buat PO → status: draft
        │
        ▼
Buat record Approval (pending)
        │
        ▼
Kadiv approve → ApprovalService::prosesPO('approved')
PO.status → approved
        │
        ▼
Vendor kirim BBM → POController::updateStatus('dikirim')
PO.status → dikirim   (canTransitionTo() check)
        │
        ▼
Admin Gudang terima BBM → GudangController::stokMasuk()
StockService::tambahStok() ← lockForUpdate()
PO.jumlah_diterima += jumlah
PO.status → diterima (jika penuh) / tetap dikirim (jika sebagian)
        │
        ▼
Admin tutup → POController::close()
PO.status → closed
```

---

## 🛣️ Routes Lengkap

```php
// routes/web.php

use App\Http\Controllers\{
    AuthController, DashboardController, OperationalController,
    GudangController, POController, VendorController,
    ApprovalController, ReportController, UserController,
    KendaraanController, BBMController
};
use Illuminate\Support\Facades\Route;

// ── Auth ────────────────────────────────────────────────
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Protected Routes ────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', fn() => redirect()->route('dashboard'));

    // Operasional BBM
    Route::prefix('operational')->name('operational.')->group(function () {
        Route::get('/',                        [OperationalController::class, 'index'])->name('index');
        Route::get('/create',                  [OperationalController::class, 'create'])->name('create');
        Route::post('/',                       [OperationalController::class, 'store'])->name('store');
        Route::get('/{transaksiBBM}',          [OperationalController::class, 'show'])->name('show');
        Route::get('/monitoring/odometer',     [OperationalController::class, 'monitoringOdometer'])->name('odometer');
        Route::get('/kendaraan/{kendaraan}/info', [OperationalController::class, 'getKendaraanInfo'])->name('kendaraan-info');
    });

    // Gudang
    Route::prefix('gudang')->name('gudang.')->group(function () {
        Route::get('/',                  [GudangController::class, 'index'])->name('index');
        Route::get('/kartu-stok',        [GudangController::class, 'kartuStok'])->name('kartu-stok');
        Route::get('/stok-masuk',        [GudangController::class, 'formStokMasuk'])->name('stok-masuk.form');
        Route::post('/stok-masuk',       [GudangController::class, 'stokMasuk'])->name('stok-masuk');
        Route::patch('/stok/{stok}/batas',[GudangController::class, 'updateBatasStok'])->name('update-batas');
    });

    // Purchase Order
    Route::resource('po', POController::class)->except(['edit','update','destroy']);
    Route::patch('/po/{po}/status', [POController::class, 'updateStatus'])->name('po.update-status');
    Route::patch('/po/{po}/close',  [POController::class, 'close'])->name('po.close');

    // Approval
    Route::prefix('approval')->name('approval.')->group(function () {
        Route::get('/',         [ApprovalController::class, 'index'])->name('index');
        Route::post('/proses',  [ApprovalController::class, 'proses'])->name('proses');
        Route::get('/riwayat', [ApprovalController::class, 'riwayat'])->name('riwayat');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/efisiensi',        [ReportController::class, 'efisiensi'])->name('efisiensi');
        Route::get('/biaya',            [ReportController::class, 'biaya'])->name('biaya');
        Route::get('/anomali',          [ReportController::class, 'anomali'])->name('anomali');
        Route::get('/export/efisiensi', [ReportController::class, 'exportEfisiensi'])->name('export.efisiensi');
    });

    // Master Data
    Route::resource('kendaraan', KendaraanController::class);
    Route::resource('bbm', BBMController::class)->except(['show','destroy']);
    Route::resource('vendor', VendorController::class);

    // User Management
    Route::resource('user', UserController::class)->except(['show']);
    Route::patch('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');
});
```

---

## ⚡ Fitur Baru Laravel 13 yang Dipakai

### 1. PHP Attributes di Model (Opsional)
> Laravel 13 menambahkan 36+ PHP Attribute baru sebagai alternatif class properties.
> Keduanya valid — pilih salah satu, jangan campur dalam satu model.

```php
// Cara lama (Laravel 12 ke bawah) — masih valid di Laravel 13
class TransaksiBBM extends Model
{
    protected $table    = 'transaksi_bbm';
    protected $fillable = ['no_transaksi', 'kendaraan_id', 'jumlah_liter', ...];
    protected $hidden   = ['deleted_at'];
    protected $casts    = ['tanggal_pemakaian' => 'date'];
}

// Cara baru (Laravel 13 PHP Attributes) — lebih deklaratif
#[Table('transaksi_bbm')]
#[Fillable(['no_transaksi', 'kendaraan_id', 'jumlah_liter'])]
#[Hidden(['deleted_at'])]
class TransaksiBBM extends Model {}
```

### 2. Cache::touch()
```php
// Perpanjang TTL cache tanpa mengambil nilainya
Cache::touch('stok-summary', now()->addHours(2));
```

### 3. Typed Config
```php
// Ambil config dengan type safety
$debug = config()->boolean('app.debug');
$limit = config()->integer('bbm.stok_minimum_default', 500);
```

### 4. Route Conflicts Detection
```bash
# Laravel 13: deteksi konflik route sebelum deploy
php artisan route:conflicts
```

### 5. bootstrap/app.php (Sama seperti Laravel 12)
```php
// Tidak ada perubahan struktur dari Laravel 12
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'       => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'audit'      => AuditLogMiddleware::class,
        ]);
    })
    ->create();
```

---

## 🔑 Kredensial Default

> Password semua akun: **`password`**

| Role | Email |
|---|---|
| Super Admin | superadmin@bbm.com |
| Admin | admin@bbm.com |
| Kadiv | kadiv@bbm.com |
| Petugas Operasional | petugas@bbm.com |
| Admin Gudang | gudang@bbm.com |
| Admin Pengadaan | pengadaan@bbm.com |
| Viewer | viewer@bbm.com |

> ⚠️ **Wajib ganti password semua akun setelah deploy ke production.**

---

## 🔐 Keamanan Sistem

| Lapisan | Implementasi | Keterangan |
|---|---|---|
| Autentikasi | Laravel Breeze + session | Login berbasis session |
| Otorisasi | Spatie Permission v7 | Role middleware + Form Request authorize() |
| CSRF | Bawaan Laravel (`@csrf`) | Semua form |
| SQL Injection | Eloquent ORM | Tidak ada raw query tanpa binding |
| Mass Assignment | `$fillable` di semua model | Atau pakai `#[Fillable]` attribute |
| Password | Cast `hashed` di User model | Otomatis hash via Laravel 13 |
| Rate Limiting | Login max 5x/menit per IP | Via `RateLimiter` facade |
| Race Condition | `lockForUpdate()` + `DB::transaction()` | Di semua operasi stok |
| Approval Security | Role check di backend (Service layer) | Frontend tidak bisa manipulasi |
| Status Lifecycle | `canTransitionTo()` di model PO | PO tidak bisa lompat status |
| Soft Delete | Semua model utama | Data histori tetap aman |
| Audit Trail | `AuditLog` + `AuditLogMiddleware` | Semua CRUD, login, approve, export |
| Akun Nonaktif | Cek `is_active` saat login | Langsung logout jika nonaktif |

---

## 💡 Perintah Artisan Berguna

```bash
# Cek versi Laravel
php artisan --version

# Reset database + seed ulang (development only)
php artisan migrate:fresh --seed

# Cek semua route terdaftar
php artisan route:list

# Deteksi konflik route (fitur baru Laravel 13)
php artisan route:conflicts

# Reset permission cache Spatie
php artisan permission:cache-reset

# Optimasi untuk production
php artisan optimize

# Buka tinker untuk test Eloquent query
php artisan tinker

# Buat model + migration + controller sekaligus
php artisan make:model NamaModel -msc
```

---

<div align="center">

**Sistem Pengawasan BBM Industri**
Laravel 13.8 · PHP 8.3+ · Laragon 8.6.1

Terakhir diperbarui: Mei 2026

</div>
