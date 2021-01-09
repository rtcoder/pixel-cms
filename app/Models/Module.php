<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    const USERS_MODULE = 1;
    const ROLES_MODULE = 2;
    const CONTACTS_MODULE = 3;
    const DOCUMENTS_MODULE = 4;
    const WWW_MODULE = 5;
    const MAILING_MODULE = 6;

    const MODULES = [
        self::USERS_MODULE,
        self::ROLES_MODULE,
        self::CONTACTS_MODULE,
        self::DOCUMENTS_MODULE,
        self::WWW_MODULE,
        self::MAILING_MODULE
    ];
}
