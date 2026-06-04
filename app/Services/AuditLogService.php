<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * Log an action performed on a model.
     *
     * @param string $action
     * @param Model|null $model
     * @param int|null $modelId
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param string $keterangan
     * @return void
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $keterangan = ''
    ): void {
        if (!config('app.debug', false)) {
            return;
        }

        $user = auth()->user();

        $modelClass = $model
            ? get_class($model)
            : ($user ? User::class : 'System');

        AuditLog::create([
            'user_id'      => $user?->id,
            'action'       => $action,
            'model'        => $modelClass,
            'model_id'     => $modelId ?? $model?->id ?? $user?->id,
            'old_values'   => $oldValues ?? [],
            'new_values'   => $newValues ?? [],
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'url'          => request()->fullUrl(),
            'method'       => request()->method(),
            'keterangan'   => $keterangan,
        ]);
    }

    /**
     * Get the difference between two arrays.
     *
     * @param array $old
     * @param array $new
     * @return array
     */
    public static function getDiff(array $old, array $new): array
    {
        $diff = [];

        foreach ($new as $key => $value) {
            if (array_key_exists($key, $old) && $old[$key] !== $value) {
                $diff[] = [
                    'key' => $key,
                    'old' => $old[$key],
                    'new' => $value,
                ];
            }
        }

        return $diff;
    }
}