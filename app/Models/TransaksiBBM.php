<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiBBM extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaksi_bbm';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_transaksi',
        'kendaraan_id',
        'bbm_id',
        'stok_id',
        'user_id',
        'jumlah_liter',
        'harga_per_liter',
        'total_biaya',
        'odometer_sebelum',
        'odometer_sesudah',
        'jarak_tempuh',
        'efisiensi',
        'status',
        'approved_by',
        'approved_at',
        'alasan_reject',
        'catatan',
        'tanggal_pemakaian',
        'lokasi_pengisian',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'jumlah_liter'              => 'float',
        'harga_per_liter'           => 'float',
        'total_biaya'               => 'float',
        'odometer_sebelum'          => 'integer',
        'odometer_sesudah'          => 'integer',
        'jarak_tempuh'              => 'integer',
        'efisiensi'                 => 'float',
        'approved_at'               => 'datetime',
        'tanggal_pemakaian'         => 'date',
    ];

    /**
     * Get the label for the transaction status.
     */
    public function statusLabel(): string
    {
        return $this->status;
    }

    /**
     * Get the badge for the transaction status.
     */
    public function statusBadge(): string
    {
        return match ($this->status) {
            'pending'   => 'bg-warning text-white',
            'approved'  => 'bg-success text-white',
            'rejected'  => 'bg-danger text-white',
            default     => 'bg-secondary text-white',
        };
    }

    /**
     * Check if the transaction is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the transaction is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the transaction has been approved.
     */
    public function hasApproval(): bool
    {
        return $this->approved_by !== null;
    }

    /**
     * Get the approval for this transaction.
     */
    public function approval(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the vehicle associated with this transaction.
     */
    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    /**
     * Get the BBM associated with this transaction.
     */
    public function bbm(): BelongsTo
    {
        return $this->belongsTo(BBM::class);
    }

    /**
     * Get the stock associated with this transaction.
     */
    public function stok(): BelongsTo
    {
        return $this->belongsTo(Stok::class);
    }

    /**
     * Get the user who made this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the person who approved this transaction.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the distance traveled by this transaction.
     */
    public function jarakTempuh(): ?int
    {
        if ($this->odometer_sesudah && $this->odometer_sebelum) {
            return $this->odometer_sesudah - $this->odometer_sebelum;
        }
        return null;
    }

    /**
     * Get the distance traveled by this transaction.
     */
    public function getJarakTempuhAttribute(): ?int
    {
        if ($this->odometer_sesudah && $this->odometer_sebelum) {
            return $this->odometer_sesudah - $this->odometer_sebelum;
        }
        return null;
    }

    /**
     * Get the efficiency of this transaction.
     */
    public function getEfisiensiAttribute(): ?float
    {
        if ($this->jarak_tempuh && $this->jumlah_liter > 0) {
            return $this->jarak_tempuh / $this->jumlah_liter;
        }
        return null;
    }

    /**
     * Generate a new transaction number.
     */
    public static function generateNoTransaksi(): string
    {
        return 'TRX-' . date('Ymd') . '-' . str_pad(
            TransaksiBBM::count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Get the transactions pending approval.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the transactions that are approved.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Get the transactions of this month.
     */
    public function scopeBulanIni($query)
    {
        $month = now()->startOfMonth();
        return $query->where('tanggal_pemakaian', '>=', $month);
    }

    /**
     * Get the transactions by vehicle.
     */
    public function scopeByKendaraan($query, int $kendaraanId)
    {
        return $query->where('kendaraan_id', $kendaraanId);
    }
}
