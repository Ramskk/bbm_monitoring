<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PO extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'po';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_po',
        'vendor_id',
        'bbm_id',
        'jumlah_liter',
        'harga_per_liter',
        'total_nilai',
        'jumlah_diterima',
        'tanggal_po',
        'tanggal_kirim_rencana',
        'tanggal_kirim_aktual',
        'tanggal_terima',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'received_by',
        'alasan_reject',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'jumlah_liter' => 'float',
        'harga_per_liter' => 'float',
        'total_nilai' => 'float',
        'jumlah_diterima' => 'float',
        'tanggal_po' => 'date',
        'tanggal_kirim_rencana' => 'date',
        'tanggal_kirim_aktual' => 'date',
        'tanggal_terima' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the badge for the PO status.
     */
    public function statusBadge(): string
    {
        return match ($this->status) {
            'draft' => 'bg-secondary text-white',
            'approved' => 'bg-primary text-white',
            'dikirim' => 'bg-info text-white',
            'diterima' => 'bg-success text-white',
            'closed' => 'bg-secondary text-white',
            'rejected' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Get the remaining delivery.
     */
    public function sisaKiriman(): float
    {
        if ($this->status === 'dikirim' && $this->jumlah_diterima > 0) {
            return $this->jumlah_liter - $this->jumlah_diterima;
        }

        return 0;
    }

    /**
     * Get the label for the PO status.
     */
    public function statusLabel(): string
    {
        return $this->status;
    }

    /**
     * Check if the PO is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Generate nomor PO unik.
     */
    public static function generateNoPO(): string
    {
        return 'PO-'.date('Ymd').'-'.str_pad(
            PO::count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Get the vendors associated with this PO.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the BBM associated with this PO.
     */
    public function bbm(): BelongsTo
    {
        return $this->belongsTo(BBM::class);
    }

    /**
     * Get the creator of this PO.
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the approver of this PO.
     */
    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the recipient of this PO.
     */
    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the approval for this PO.
     */
    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }

    /**
     * Get the POs of this user.
     */
    public function posBuatan(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the status lifecycle.
     */
    public const STATUS_ORDER = [
        'draft',
        'approved',
        'dikirim',
        'diterima',
        'closed',
    ];

    /**
     * Check if the PO can transition to the given status.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $current = $this->status;

        return match ($current) {
            'draft' => in_array($newStatus, ['approved', 'dikirim'], true),
            'approved' => in_array($newStatus, ['dikirim', 'diterima'], true),
            'dikirim' => in_array($newStatus, ['diterima'], true),
            'diterima' => in_array($newStatus, ['closed'], true),
            'closed' => in_array($newStatus, [], true),
            'rejected' => in_array($newStatus, [], true),
            default => in_array($newStatus, ['draft'], true),
        };
    }

    /**
     * Get the POs in draft status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
