<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class MailHelper
{
    /**
     * @param string $view
     * @param array $data
     * @param string $to
     * @param string $subject
     */
    public static function send(string $view, array $data, string $to, string $subject)
    {
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject);
            $message->from(env('MAIL_USERNAME'), env('APP_NAME'));
        });
    }
}
