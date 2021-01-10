<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string name
 * @property array permissions
 * @property bool is_admin
 * @property integer client_id
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'permissions',
    ];

    protected $guarded = [
        'is_admin',
        'client_id',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];
}
