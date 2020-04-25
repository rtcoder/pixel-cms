<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'client_id',
    ];

    protected $guarded = [
        'client_id',
        'role_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = [
        'role',
        'client',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function findForPassport($email)
    {
        return $this->where([
            ['email', $email],
            ['is_active', true]
        ])->first();
    }
}
