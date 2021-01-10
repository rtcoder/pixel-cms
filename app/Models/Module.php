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

    const VIEW_ACTION = 'view';
    const CREATE_ACTION = 'create';
    const EDIT_ACTION = 'edit';
    const DELETE_ACTION = 'delete';

    const ACTIONS = [
        self::VIEW_ACTION,
        self::CREATE_ACTION,
        self::EDIT_ACTION,
        self::DELETE_ACTION,
    ];

    const MODULES = [
        self::USERS_MODULE,
        self::ROLES_MODULE,
        self::CONTACTS_MODULE,
//        self::DOCUMENTS_MODULE,
//        self::WWW_MODULE,
//        self::MAILING_MODULE
    ];

    const MODULES_PAGE_NAMES = [
        self::USERS_MODULE => 'pages.users',
        self::ROLES_MODULE => 'pages.roles',
        self::CONTACTS_MODULE => 'pages.contacts',
        self::DOCUMENTS_MODULE => 'pages.documents',
        self::WWW_MODULE => 'pages.website',
        self::MAILING_MODULE => 'pages.mailing'
    ];
}
