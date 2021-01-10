<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string value
 * @property integer contact_id
 */
class ContactEmailAddress extends Model
{
    protected $fillable = [
        'value',
    ];

    protected $guarded = [
        'contact_id'
    ];

}
