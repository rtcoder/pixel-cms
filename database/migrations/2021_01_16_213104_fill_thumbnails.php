<?php

use App\Models\Media;
use Illuminate\Database\Migrations\Migration;

class FillThumbnails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Media::all() as $item) {
            $item->thumbnails = [
                $item->filename . '?x=200'
            ];
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
