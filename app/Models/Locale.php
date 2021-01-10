<?php

namespace App\Models;

class Locale
{
    const PL = 'pl';
    const EN = 'en';

    const LOCALES = [
        self::PL,
        self::EN,
    ];

    const LOCALES_NAMES = [
        self::PL => 'locales.pl',
        self::EN => 'locales.en',
    ];
}
