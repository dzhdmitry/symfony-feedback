<?php

namespace AppBundle\Util;

class StringHelper
{
    /**
     * @param int $length
     * @return string
     */
    public static function uniqueRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $string = "";

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $string;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
