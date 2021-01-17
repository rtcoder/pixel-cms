<?php

namespace App\Helpers;

class Helpers
{
    /**
     * @param int $length
     * @return false|string
     */
    public static function generatePassword($length = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * @param string $dir
     */
    public static function rmDir(string $dir)
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
}
