<?php

namespace App\Models;

use App\Events\MediaDeleting;
use App\Helpers\MediaHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string filename
 * @property string type
 * @property string extension
 * @property string url
 * @property string path
 * @property array dimensions
 * @property integer size
 * @property integer client_id
 * @property bool is_public
 */
class Media extends Model
{
    const COMPRESSION_QUALITY = 80;

    protected $guarded = [
        'filename',
        'type',
        'extension',
        'dimensions',
        'size',
        'is_public',
        'client_id',
    ];

    protected $appends = [
        'url',
    ];

    protected $dispatchesEvents = [
        'deleting' => MediaDeleting::class
    ];

    protected $casts = [
        'dimensions' => 'array'
    ];

    public function getUrlAttribute(): string
    {
        return env('APP_URL') . '/client/storage/' . $this->filename;
    }

    public function getPathAttribute(): string
    {
        return MediaHelper::getMediaStoragePath($this->filename);
    }
}
