<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactEmailAddress extends Model
{
    protected $fillable = [
        'value',
    ];

    protected $guarded = [
        'client_id'
    ];

}
