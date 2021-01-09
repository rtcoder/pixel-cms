<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class MailHelper
{

    public static function send(string $view, array $data, string $to, string $subject)
    {
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject);
            $message->from(env('MAIL_USERNAME'), env('APP_NAME'));
        });
    }
}
