<?php

namespace App\Helpers;


use App\Helpers\Media\ImageHelper;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use ImagickException;

class MediaHelper
{
    /**
     * @param Media $media
     */
    public static function deleteMediaFile(Media $media)
    {
        @unlink($media->filename);
        Helpers::rmDir(explode('.', $media->filename)[0]);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getMediaStoragePath(string $name = ''): string
    {
        return storage_path('app/public/media') . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * @param UploadedFile $file
     * @param int $client_id
     * @return Media
     * @throws ImagickException
     */
    public static function storeMedia(UploadedFile $file, int $client_id): Media
    {
        if (!file_exists(MediaHelper::getMediaStoragePath())) {
            mkdir(MediaHelper::getMediaStoragePath(), 0777, true);
        }
        $type = explode('/', $file->getClientMimeType());
        $extension = $file->getClientOriginalExtension();
        $filename = Carbon::now()->format('Y-m-d_H-i-s-u') . '.' . $extension;

        $media = new Media();
        $media->client_id = $client_id;
        $media->original_name = $file->getClientOriginalName();
        $media->filename = $filename;
        $media->size = $file->getSize();
        $media->type = join('/', $type);
        $media->extension = $extension;
        $media->is_public = true;


        // Mime type filter should be added here in the future
        switch ($type[0]) {
            case 'image':
                $dimensions = getimagesize(MediaHelper::getMediaStoragePath($filename));
                $media->dimensions = $dimensions ? ['width' => $dimensions[0], 'height' => $dimensions[1]] : [];
                $media->thumbnails = [
                    $media->filename . '?x=200'
                ];

                if ($file->getClientMimeType() == 'image/x-icon') {
                    $file->storeAs('media', $filename, 'public');
                } else {
                    $imagick = ImageHelper::compressImage($file);
                    ImageHelper::autoRotateImage($imagick);
                    $imagick->setFilename($filename);
                    $imagick->writeImage(MediaHelper::getMediaStoragePath($filename));
                }
                break;
            default:
                $file->storeAs('media', $filename, 'public');
        }

        $media->save();

        return $media;
    }
}
