<?php

namespace App\Helpers\Media;


use App\Models\Media;
use App\Models\MediaSizes;
use Exception;
use Illuminate\Http\UploadedFile;
use Imagick;
use ImagickException;

class ImageHelper
{
    /**
     * @param Media $media
     * @param MediaSizes $sizes
     * @param bool $returnUrl
     * @return string
     * @throws ImagickException
     */
    public static function getResizedOrCreate(Media $media, MediaSizes $sizes, $returnUrl = true): string
    {
        if (explode('/', $media->type)[0] != 'image') {
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

    /**
     * @param Imagick $image
     */
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
     * @return Imagick
     * @throws ImagickException
     */
    public static function compressImage(UploadedFile $file): Imagick
    {
        $imagick = new Imagick();
        $imagick->readImage($file->getRealPath());
        $imagick->setImageCompressionQuality(Media::COMPRESSION_QUALITY);
        self::autoRotateImage($imagick);
        return $imagick;
    }
}
