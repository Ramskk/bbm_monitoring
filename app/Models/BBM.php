<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BBM extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bbm';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'harga_per_liter',
        'is_active',
        'keterangan',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'harga_per_liter' => 'integer',
        'is_active'       => 'boolean',
    ];

    /**
     * Get the label for the BBM type.
     */
    public function jenisLabel(): string
    {
        return $this->jenis;
    }

    /**
     * Get the available stock for this BBM.
     */
    public function stokTersedia(): ?int
    {
        return Stok::where('bbm_id', $this->id)
            ->whereColumn('jumlah', '>=', 'stok_minimum')
            ->first()
            ?->jumlah ?? null;
    }

    /**
     * Check if the stock is critical (below minimum).
     */
    public function isStokKritis(): bool
    {
        $stok = Stok::where('bbm_id', $this->id)->first();
        return $stok
            && $stok->jumlah < $stok->stok_minimum;
    }

    /**
     * Get the stock for this BBM.
     */
    public function stok(): HasOne
    {
        return $this->hasOne(Stok::class);
    }

    /**
     * Get the transactions for this BBM.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiBBM::class);
    }

    /**
     * Get the purchase orders for this BBM.
     */
    public function po(): HasMany
    {
        return $this->hasMany(PO::class);
    }

    /**
     * Get the stock mutations for this BBM.
     */
    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class);
    }

    /**
     * Get the vehicles using this BBM.
     */
    public function kendaraan(): HasMany
    {
        return $this->hasMany(Kendaraan::class);
    }
}
