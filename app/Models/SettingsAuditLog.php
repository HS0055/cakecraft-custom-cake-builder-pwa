<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class SettingsAuditLog
 *
 * Logs trackable actions performed by administrators, specifically for modifications made to
 * system-wide settings, themes, keys, and values within the settings interface.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $group
 * @property string $key
 * @property array|null $old_value
 * @property array|null $new_value
 * @property string|null $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read \App\Models\User|null $user
 */
class SettingsAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'group',
        'key',
        'old_value',
        'new_value',
        'ip_address',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
