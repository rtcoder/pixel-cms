<?php

namespace App;

use App\Traits\ReturnsWithArray;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use ReturnsWithArray;

    protected $fillable = [
        'name',
        'permissions',
    ];

    protected $guarded = [
        'is_admin',
        'client_id'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

}
