<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string name
 * @property array permissions
 * @property bool is_admin
 * @property bool is_super_admin
 * @property integer client_id
 * @property integer type
 */
class Role extends Model
{
    use HasFactory;

    const SUPER_ADMIN = 1000;
    const ADMIN = 100;
    const USER = 1;

    protected $fillable = [
        'name',
        'permissions',
    ];

    protected $guarded = [
        'type',
        'client_id',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    protected $appends = [
        'is_admin',
        'is_super_admin',
    ];

    public function getIsAdminAttribute(): bool
    {
        return $this->type === self::ADMIN;
    }

    public function getIsSuperAdminAttribute(): bool
    {
        return $this->type === self::SUPER_ADMIN;
    }
}
