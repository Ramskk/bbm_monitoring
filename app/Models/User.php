<?php

namespace App\Models;

// Menggunakan Authenticatable bawaan Laravel untuk sistem login
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'employee_id',
        'departemen',
        'jabatan',
        'telepon',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Update data login terakhir user.
     * Fungsi ini dipanggil di AuthController setelah login sukses.
     * * @param string|null $ip
     * @return bool
     */
    public function updateLastLogin(?string $ip): bool
    {
        return $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Get all transactions for this user.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiBBM::class);
    }

    /**
     * Get all approvals for this user.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approved_by');
    }

    /**
     * Get all audit logs for this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get all POs created by this user.
     */
    public function posBuatan(): HasMany
    {
        return $this->hasMany(PO::class, 'created_by');
    }

    /**
     * Get all stock mutations for this user.
     */
    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class);
    }

    /**
     * Get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}