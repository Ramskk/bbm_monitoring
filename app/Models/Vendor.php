<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'kota',
        'telepon',
        'email',
        'npwp',
        'kontak_person',
        'kontak_telepon',
        'rekening_bank',
        'nama_bank',
        'atas_nama',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relasi ke Purchase Order.
     */
    public function po(): HasMany
    {
        return $this->hasMany(PO::class, 'vendor_id');
    }

    /**
     * Badge status.
     */
    public function statusBadge(): string
    {
        return match ($this->status) {
            'Aktif' => 'bg-success text-white',
            'Non_Aktif' => 'bg-secondary text-white',
            default => 'bg-warning text-white',
        };
    }

    /**
     * Status aktif.
     */
    public function isAktif(): bool
    {
        return $this->status === 'Aktif';
    }

    /**
     * Scope vendor aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Generate kode vendor.
     */
    public static function generateKode(): string
    {
        $lastVendor = self::withTrashed()
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastVendor
            ? ((int) preg_replace('/[^0-9]/', '', $lastVendor->kode)) + 1
            : 1;

        return 'VND-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Cek apakah vendor punya PO aktif.
     */
    public function hasActivePOs(): bool
    {
        return $this->po()
            ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
            ->exists();
    }

    /**
     * Accessor badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->status === 'Aktif'
            ? 'Aktif'
            : 'Non Aktif';
    }
}