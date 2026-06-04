<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vendor';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the badge for the vendor status.
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
     * Check if the vendor is active.
     */
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Get the purchase orders for this vendor.
     */
    public function po(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the active vendors.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Generate a new vendor code.
     */
    public static function generateKode(): string
    {
        $suffix = str_pad(Vendor::count() + 1, 3, '0', STR_PAD_LEFT);
        return 'VND-' . $suffix;
    }

    /**
     * Check if the vendor has active POs.
     */
    public function hasActivePOs(): bool
    {
        return $this->po()->where('status', '!=', 'closed')
            ->whereNotNull('created_by')
            ->exists();
    }
}
