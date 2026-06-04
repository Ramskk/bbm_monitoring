<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MutasiStok extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mutasi_stok';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stok_id',
        'bbm_id',
        'jenis',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'harga_per_liter',
        'referensi_no',
        'referensi_type',
        'referensi_id',
        'user_id',
        'keterangan',
        'tanggal',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'jumlah'        => 'integer',
        'stok_sebelum'  => 'integer',
        'stok_sesudah'  => 'integer',
        'harga_per_liter' => 'float',
        'tanggal'       => 'date',
    ];

    /**
     * Get the label for the mutation type.
     */
    public function jenisLabel(): string
    {
        return $this->jenis;
    }

    /**
     * Get the badge for the mutation type.
     */
    public function jenisBadge(): string
    {
        return match ($this->jenis) {
            'masuk'   => 'bg-success text-white',
            'keluar'  => 'bg-danger text-white',
            default   => 'bg-secondary text-white',
        };
    }

    /**
     * Get the value of the mutation.
     */
    public function nilaiMutasi(): int
    {
        return match ($this->jenis) {
            'masuk'   => $this->jumlah,
            'keluar'  => -$this->jumlah,
            default   => $this->jumlah,
        };
    }

    /**
     * Get the stock associated with this mutation.
     */
    public function stok(): BelongsTo
    {
        return $this->belongsTo(Stok::class);
    }

    /**
     * Get the BBM associated with this mutation.
     */
    public function bbm(): BelongsTo
    {
        return $this->belongsTo(BBM::class);
    }

    /**
     * Get the user who made this mutation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
