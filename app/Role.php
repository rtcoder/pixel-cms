<?php

namespace App;

use App\Traits\ReturnsWithArray;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use ReturnsWithArray;

    protected $fillable = [
        'name',
    ];

    protected $guarded = [
        'is_admin'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

}
