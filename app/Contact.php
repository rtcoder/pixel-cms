<?php

namespace App;

use App\Traits\ReturnsWithArray;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use ReturnsWithArray;

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
        'phone_numbers',
        'email_addresses',
    ];

    protected $guarded = [
        'client_id'
    ];

    protected $casts = [
        'phone_numbers' => 'array',
        'email_addresses' => 'array',
    ];

}
