<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'approval';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'requested_by',
        'approved_by',
        'status',
        'urutan',
        'catatan_peminta',
        'catatan_approver',
        'processed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status'       => 'string',
        'urutan'       => 'integer',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the label for the assignment status.
     */
    public function statusLabel(): string
    {
        return $this->status;
    }

    /**
     * Check if the assignment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the assignment is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the assignment is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the approvable item (polymorphic relation).
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the person who requested this assignment.
     */
    public function peminta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the person who approved this assignment.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the pending assignments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
