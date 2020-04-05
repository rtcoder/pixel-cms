<?php

namespace App;

use App\Events\ClientCreated;
use App\Traits\ReturnsWithArray;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use ReturnsWithArray;

    protected $fillable = [
        'slug',
        'name',
        'email',
        'phone_number',
        'locale',
        'available_locales',
        'modules',
    ];

    protected $guarded = [
    ];

    protected $casts = [
        'available_locales' => 'array',
        'modules' => 'array',
    ];

    protected $dispatchesEvents = [
        'created' => ClientCreated::class
    ];

    protected $appends = [
        'is_superadmin'
    ];

    protected $with = [
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getIsSuperadminAttribute()
    {
        return $this->slug === 'developer';
    }
}
