<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
    ];

    protected $guarded = [
        'client_id'
    ];

    protected $with = [
        'phoneNumbers',
        'emailAddresses',
    ];

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(ContactPhoneNumber::class);
    }

    public function emailAddresses(): HasMany
    {
        return $this->hasMany(ContactEmailAddress::class);
    }

}
