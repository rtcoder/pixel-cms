<?php

namespace App\Models;

use App\Events\MediaDeleting;
use App\Helpers\MediaHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string original_name
 * @property string filename
 * @property string type
 * @property string extension
 * @property string url
 * @property string path
 * @property array dimensions
 * @property array thumbnails
 * @property integer size
 * @property integer client_id
 * @property bool is_public
 */
class Media extends Model
{
    const COMPRESSION_QUALITY = 80;

    protected $guarded = [
        'original_name',
        'filename',
        'type',
        'extension',
        'dimensions',
        'size',
        'is_public',
        'client_id',
        'thumbnails',
    ];

    protected $appends = [
        'url',
        'readable_type',
        'thumbnails_urls',
    ];

    protected $dispatchesEvents = [
        'deleting' => MediaDeleting::class
    ];

    protected $casts = [
        'dimensions' => 'array',
        'thumbnails' => 'array',
    ];

    public function getUrlAttribute(): string
    {
        return env('APP_URL') . '/client/storage/' . $this->filename;
    }

    public function getThumbnailsUrlsAttribute(): array
    {
        return array_map(function ($value) {
            return env('APP_URL') . '/client/storage/' . $value;
        }, $this->thumbnails);
    }

    public function getPathAttribute(): string
    {
        return MediaHelper::getMediaStoragePath($this->filename);
    }

    public function getReadableTypeAttribute(): string
    {
        $types = [
            'image/jpeg' => __('media-types.image'),
            'image/jpg' => __('media-types.image'),
            'image/gif' => __('media-types.gif'),
            'image/bmp' => __('media-types.bitmap'),
            'image/png' => __('media-types.image'),
            'video/mp4' => __('media-types.video'),
        ];
        return $types[$this->type] ?? $this->type;
    }
}
