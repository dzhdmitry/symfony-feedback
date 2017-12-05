<?php

namespace AppBundle\Exception;

class PictureHandlerException extends \Exception
{
    /**
     * @param $text
     * @param $path
     * @return PictureHandlerException
     */
    public static function create($text, $path)
    {
        return new self(sprintf($text, $path));
    }
}
