<?php

namespace App\Models;

use App\Events\ClientCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property string slug
 * @property string name
 * @property string email
 * @property string phone_number
 * @property string locale
 * @property array available_locales
 * @property array modules
 * @property bool is_super_admin
 */
class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'locale',
        'available_locales',
    ];

    protected $casts = [
        'available_locales' => 'array',
        'modules' => 'array',
    ];

    protected $dispatchesEvents = [
        'created' => ClientCreated::class
    ];

    protected $appends = [
        'is_super_admin',
    ];

    protected $guarded = [
        'slug',
        'modules',
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
