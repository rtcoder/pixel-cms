<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property string first_name
 * @property string last_name
 * @property string company
 * @property integer client_id
 * @property string full_name
 * @property ContactPhoneNumber[] phoneNumbers
 * @property ContactEmailAddress[] emailAddresses
 */
class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
    ];

    protected $guarded = [
        'client_id',
    ];

    protected $with = [
        'phoneNumbers',
        'emailAddresses',
    ];

    protected $appends=[
        'full_name',
    ];

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(ContactPhoneNumber::class);
    }

    public function emailAddresses(): HasMany
    {
        return $this->hasMany(ContactEmailAddress::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

}
