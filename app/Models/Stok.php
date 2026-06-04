<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stok extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stok';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bbm_id',
        'jumlah',
        'stok_minimum',
        'stok_maksimum',
        'lokasi',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'jumlah'         => 'integer',
        'stok_minimum'   => 'integer',
        'stok_maksimum'  => 'integer',
    ];

    /**
     * Get the percentage of the stock.
     */
    public function getPersentase(): ?float
    {
        $maksimum = $this->stok_maksimum;
        if ($maksimum <= 0) {
            return null;
        }

        return ($this->jumlah / $maksimum) * 100;
    }

    /**
     * Get the status warning for the stock.
     */
    public function statusWarn(): string
    {
        $minimum = $this->stok_minimum;
        $maksimum = $this->stok_maksimum;

        if ($this->jumlah >= $maksimum) {
            return 'penuh';
        }

        if ($this->jumlah <= $minimum) {
            return 'kritis';
        }

        return 'normal';
    }

    /**
     * Check if the stock is critical (below minimum).
     */
    public function isKritis(): bool
    {
        return $this->jumlah <= $this->stok_minimum;
    }

    /**
     * Check if the stock is full (at or above maximum).
     */
    public function isPenuh(): bool
    {
        return $this->jumlah >= $this->stok_maksimum;
    }

    /**
     * Get the BBM associated with this stock.
     */
    public function bbm(): BelongsTo
    {
        return $this->belongsTo(BBM::class);
    }

    /**
     * Get the mutations for this stock.
     */
    public function mutasi(): HasMany
    {
        return $this->hasMany(MutasiStok::class);
    }
}
