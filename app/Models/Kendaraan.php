<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kendaraan';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomor_polisi',
        'nama',
        'merek',
        'model',
        'tahun',
        'jenis',
        'bbm_id',
        'kapasitas_tangki',
        'konsumsi_bbm_standar',
        'odometer_awal',
        'odometer_terakhir',
        'departemen',
        'pengemudi_default',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tahun'              => 'integer',
        'kapasitas_tangki'   => 'integer',
        'konsumsi_bbm_standar'=> 'float',
        'odometer_awal'      => 'integer',
        'odometer_terakhir'  => 'integer',
    ];

    /**
     * Get the label for the vehicle type.
     */
    public function jenisLabel(): string
    {
        return $this->jenis;
    }

    /**
     * Get the average efficiency of this vehicle.
     */
    public function rataEfisiensi(): ?float
    {
        $transaksi = $this->transaksi()
            ->where('status', 'approved')
            ->whereNotNull('efisiensi')
            ->get();

        if ($transaksi->count() === 0) {
            return null;
        }

        return $transaksi->average('efisiensi');
    }

    /**
     * Get the total cost incurred by this vehicle this month.
     */
    public function totalBiayaBulanIni(): ?float
    {
        $month = now()->startOfMonth();
        $total = $this->transaksi()
            ->where('tanggal_pemakaian', '>=', $month)
            ->sum('total_biaya');

        return $total;
    }

    /**
     * Get the status label and badge for the vehicle.
     */
    public function statusLabel(): string
    {
        return $this->status;
    }

    /**
     * Get the status badge for the vehicle.
     */
    public function statusBadge(): string
    {
        return match ($this->status) {
            'aktif'    => 'bg-success text-white',
            'non_aktif' => 'bg-secondary text-white',
            default    => 'bg-warning text-white',
        };
    }

    /**
     * Get the transaction for this vehicle.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiBBM::class);
    }

    /**
     * Get the BBM used by this vehicle.
     */
    public function bbm(): BelongsTo
    {
        return $this->belongsTo(BBM::class);
    }

    /**
     * Check if the vehicle is active.
     */
    public function isAktif(): bool
    {
        return $this->status === 'aktif' && $this->is_active;
    }

    /**
     * Get the active vehicles.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
            ->where('is_active', true);
    }

    /**
     * Get the vehicles by department.
     */
    public function scopeByDepartemen($query, string $departemen)
    {
        return $query->where('departemen', $departemen);
    }
}
