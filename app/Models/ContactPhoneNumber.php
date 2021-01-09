<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactPhoneNumber extends Model
{
    protected $fillable = [
        'value',
    ];

    protected $guarded = [
        'client_id'
    ];

}
