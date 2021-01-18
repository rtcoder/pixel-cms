<?php

namespace App\Helpers;


use App\Helpers\Media\ImageHelper;
use App\Models\Media;
use Carbon\Carbon;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
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
        $filenameWithoutExt = Carbon::now()->format('Y-m-d_H-i-s-u');
        $filename = $filenameWithoutExt . '.' . $extension;

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
                if ($file->getClientMimeType() == 'image/x-icon') {
                    $file->storeAs('media', $filename, 'public');
                } else {
                    $imagick = ImageHelper::compressImage($file);
                    ImageHelper::autoRotateImage($imagick);
                    $imagick->setFilename($filename);
                    $imagick->writeImage(MediaHelper::getMediaStoragePath($filename));
                }

                $dimensions = getimagesize(MediaHelper::getMediaStoragePath($filename));
                $media->dimensions = $dimensions ? ['width' => $dimensions[0], 'height' => $dimensions[1]] : [];
                $media->thumbnails = [
                    $media->filename . '?x=200'
                ];
                break;
            case 'video':
                $file->storeAs('media', $filename, 'public');


                $ffmpeg = FFMpeg::create();
                $video = $ffmpeg->open(MediaHelper::getMediaStoragePath($filename));
                $ffProbe = FFProbe::create();

                $videoStream = $ffProbe
                    ->streams(MediaHelper::getMediaStoragePath($filename))
                    ->videos()
                    ->first();

                $dimensions = $videoStream->getDimensions();
                $duration = $videoStream->get('duration');
                $media->duration = $duration;

                $step = $duration / 5;
                $thumbnails = [];
                if (!file_exists(MediaHelper::getMediaStoragePath($filenameWithoutExt))) {
                    mkdir(MediaHelper::getMediaStoragePath($filenameWithoutExt), 0777, true);
                }

                for ($i = 1; $i <= 5; $i++) {
                    $thumbName = $filenameWithoutExt . '/frame_' . $i . '.jpg';
                    $thumbnails[] = $thumbName;
                    $video
                        ->frame(TimeCode::fromSeconds($step * $i))
                        ->save(MediaHelper::getMediaStoragePath($thumbName));
                }


                $media->dimensions = $dimensions ? [
                    'width' => $dimensions->getWidth(),
                    'height' => $dimensions->getHeight()
                ] : [];
                $media->thumbnails = $thumbnails;

                break;
            case 'audio':
                $file->storeAs('media', $filename, 'public');


                $ffmpeg = FFMpeg::create();
                $audio = $ffmpeg->open(MediaHelper::getMediaStoragePath($filename));
                $ffProbe = FFProbe::create();

                $audioStream = $ffProbe
                    ->streams(MediaHelper::getMediaStoragePath($filename))
                    ->audios()
                    ->first();

                $media->duration = $audioStream->get('duration');

                if (!file_exists(MediaHelper::getMediaStoragePath($filenameWithoutExt))) {
                    mkdir(MediaHelper::getMediaStoragePath($filenameWithoutExt), 0777, true);
                }

                $thumbName = $filenameWithoutExt . '/waveform.png';
                $thumbnails = [$thumbName];
                $audio->waveform(640, 120, ['#0099cc'])
                    ->save(MediaHelper::getMediaStoragePath($thumbName));


                $media->dimensions = [];
                $media->thumbnails = $thumbnails;

                break;
            default:
                $file->storeAs('media', $filename, 'public');
        }

        $media->save();

        return $media;
    }
}
