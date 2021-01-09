<?php

namespace App\Models;

use App\Events\ClientCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'email',
        'phone_number',
        'locale',
        'available_locales',
        'modules',
    ];

    protected $casts = [
        'available_locales' => 'array',
        'modules' => 'array',
    ];

    protected $dispatchesEvents = [
        'created' => ClientCreated::class
    ];

    protected $appends = [
        'is_super_admin'
    ];

    protected $with = [
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getIsSuperAdminAttribute(): bool
    {
        return $this->slug === 'developer';
    }
}
