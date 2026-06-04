<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audit_logs';

    /**
     * The array of attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'keterangan',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'old_values'       => 'array',
        'new_values'       => 'array',
    ];

    /**
     * Get the label for the action.
     */
    public function actionLabel(): string
    {
        $labels = [
            'create'     => 'Buat',
            'update'     => 'Ubah',
            'delete'     => 'Hapus',
            'approve'    => 'Setujui',
            'reject'     => 'Tolak',
            'login'      => 'Login',
            'logout'     => 'Logout',
            'export'     => 'Export',
        ];

        return $labels[$this->action] ?? $this->action;
    }

    /**
     * Get the badge for the action.
     */
    public function actionBadge(): string
    {
        $badges = [
            'create'    => 'bg-primary text-white',
            'update'    => 'bg-info text-white',
            'delete'    => 'bg-danger text-white',
            'approve'   => 'bg-success text-white',
            'reject'    => 'bg-danger text-white',
            'login'     => 'bg-warning text-white',
            'logout'    => 'bg-secondary text-white',
            'export'    => 'bg-purple text-white',
            'default'     => 'bg-secondary text-white',
        ];

        return $badges[$this->action] ?? $badges['default'];
    }

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
