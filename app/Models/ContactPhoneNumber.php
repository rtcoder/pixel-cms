<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string value
 * @property integer contact_id
 */
class ContactPhoneNumber extends Model
{
    protected $fillable = [
        'value',
    ];

    protected $guarded = [
        'contact_id'
    ];

}
