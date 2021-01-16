<?php

namespace App\Helpers;


use App\Models\Media;
use App\Models\MediaSizes;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Imagick;
use ImagickException;

class MediaHelper
{
    public static function rmDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object))
                        self::rmDir($dir . "/" . $object);
                    else
                        @unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }

    public static function deleteMediaFile(Media $media)
    {
        @unlink($media->filename);
        self::rmDir(explode('.', $media->filename)[0]);
    }

    public static function getMediaFromDescription($item)
    {
        preg_match_all("/src=\"" . preg_quote(env('APP_URL'), '/') . "(.*?)\"/", $item->description, $matches);
        return Media::whereIn('path', $matches[1])->where('client_id', Auth::user()->client_id)->get();
    }

    public static function getResizedOrCreate(Media $media, MediaSizes $sizes, $returnUrl = true): string
    {
        if (explode(',', $media->type)[0] != 'image') {
            return $returnUrl
                ? $media->url
                : $media->path;
        }
        $x = $sizes->x;
        $y = $sizes->y;
        if (count($media->dimensions)) {
            $imageWidth = $media->dimensions['width'];
            $imageHeight = $media->dimensions['height'];
        } else {
            list($imageWidth, $imageHeight) = getimagesize($media->path);
        }
        if (is_null($x) || is_null($y)) {
            if (is_null($x)) {
                if ($sizes->yUnit == 'px') {
                    $ratio = $imageHeight / $y;
                    $x = $imageWidth / $ratio;
                } else {
                    $heightInPx = $imageHeight * ($y / 100);
                    $ratio = $imageHeight / $heightInPx;
                    $x = $imageWidth / $ratio;
                    $y = $heightInPx;
                }
            }
            if (is_null($y)) {
                if ($sizes->xUnit == 'px') {
                    $ratio = $imageWidth / $x;
                    $y = $imageHeight / $ratio;
                } else {
                    $widthInPx = $imageWidth * ($x / 100);
                    $ratio = $imageWidth / $widthInPx;
                    $y = $imageHeight / $ratio;
                    $x = $widthInPx;
                }
            }
        } else {
            if ($sizes->yUnit == '%') {
                $heightInPx = $imageHeight * ($y / 100);
                $ratio = $imageHeight / $heightInPx;
                $x = $imageWidth / $ratio;
                $y = $heightInPx;
            } elseif ($sizes->xUnit == '%') {
                $widthInPx = $imageWidth * ($x / 100);
                $ratio = $imageWidth / $widthInPx;
                $y = $imageHeight / $ratio;
                $x = $widthInPx;
            }
        }

        $explodedPath = explode('.', $media->path);
        $filename = "/{$x}_{$y}.$explodedPath[1]";
        $thumbnailPath = "$explodedPath[0]/{$x}_{$y}.$explodedPath[1]";
        if (!file_exists($thumbnailPath)) {
            $imagick = new Imagick();
            try {
                $imagick->readImage($media->path);
                $imagick->resizeImage($x, $y, Imagick::FILTER_CATROM, 1);

                $imagick->setFilename($filename);
                if (!file_exists($explodedPath[0])) {
                    mkdir($explodedPath[0]);
                }
                $imagick->writeImage($thumbnailPath);
            } catch (Exception $e) {
                throw $e;
            }
        }
        return $returnUrl
            ? env('APP_URL') . explode('.', $media->url)[0] . $filename
            : $thumbnailPath;
    }

    public static function getMediaStoragePath(string $name = ''): string
    {
        return storage_path('app/public/media') . DIRECTORY_SEPARATOR . $name;
    }

    public static function getExifMediaExtension(int $exif)
    {
        //--Might add more in the future if needed
        $extensions = [
            null,
            '.gif',
            '.jpeg',
            '.png'
        ];
        return $extensions[$exif];
    }

    public static function autoRotateImage(Imagick $image)
    {
        $orientation = $image->getImageOrientation();

        switch ($orientation) {
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateimage("#000", 180); // rotate 180 degrees
                break;

            case Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateimage("#000", 90); // rotate 90 degrees CW
                break;

            case Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateimage("#000", -90); // rotate 90 degrees CCW
                break;
        }

        // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
        $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
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
        $size = $file->getSize();
        $filename = Carbon::now()->format('Y-m-d_H-i-s-u') . '.' . $extension;

        // Mime type filter should be added here in the future
        switch ($type[0]) {
            case 'image':
                if ($file->getClientMimeType() == 'image/x-icon') {
                    $file->storeAs('media', $filename, 'public');
                } else {
                    $imagick = self::compressImage($file);
                    MediaHelper::autoRotateImage($imagick);
                    $imagick->setFilename($filename);
                    $imagick->writeImage(MediaHelper::getMediaStoragePath($filename));
                }
                break;
            default:
                $file->storeAs('media', $filename, 'public');
        }
        $media = new Media();
        $media->client_id = $client_id;
        $media->filename = $filename;
        $media->type = join('/', $type);
        $media->extension = $extension;
        $media->is_public = true;
        $dimensions = getimagesize(MediaHelper::getMediaStoragePath($filename));
        $media->dimensions = $dimensions ? ['width' => $dimensions[0], 'height' => $dimensions[1]] : [];
        $media->size = $size;

        $media->save();

        return $media;
    }

    /**
     * @param UploadedFile $file
     * @return Imagick
     * @throws ImagickException
     */
    public static function compressImage(UploadedFile $file): Imagick
    {
        $imagick = new Imagick();
        $imagick->readImage($file->getRealPath());
        $imagick->setImageCompressionQuality(Media::COMPRESSION_QUALITY);
        MediaHelper::autoRotateImage($imagick);
        return $imagick;
    }
}
