<?php

namespace App\Listeners;

use App\Events\MediaDeleting;
use App\Helpers\MediaHelper;

class DeleteMediaFile
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param MediaDeleting $event
     * @return void
     */
    public function handle(MediaDeleting $event)
    {
        MediaHelper::deleteMediaFile($event->media);
    }
}
