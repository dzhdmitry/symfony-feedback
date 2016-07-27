<?php

namespace AppBundle\Service;

use AppBundle\Entity\Message;
use AppBundle\Entity\Picture;
use AppBundle\Exception\PictureHandlerException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureHandler
{
    const MAX_WIDTH = 320;
    const MAX_HEIGHT = 240;

    protected $directory;

    protected $folder;

    public function __construct($publicDir, $folder)
    {
        $this->directory = $publicDir.$folder;
        $this->folder = $folder;
    }

    /**
     * @param Picture $picture
     */
    public function resize(Picture $picture)
    {
        $path = $picture->getAbsolutePath();

        if (!is_file($path)) {
            self::throwPictureHandlerException("File '%s' does not exist", $path);
        }

        if (!is_writable($picture->getAbsolutePath())) {
            self::throwPictureHandlerException("File '%s' is not writable", $path);
        }

        $img = new \Imagick($path);
        $width = $img->getImageWidth();
        $height = $img->getImageHeight();
        $wOut = $width > self::MAX_WIDTH;
        $hOut = $height > self::MAX_HEIGHT;

        if ($wOut || $hOut) {
            // resize needed
            $widthRatio = self::MAX_WIDTH / $width;
            $heightRatio = self::MAX_HEIGHT / $height;

            if ($wOut && !$hOut) {
                // decrease width
                $newWidth = self::MAX_WIDTH;
                $newHeight = $height * $widthRatio;
            } elseif (!$wOut && $hOut) {
                // decrease height
                $newWidth = $width * $heightRatio;
                $newHeight = self::MAX_HEIGHT;
            } else {
                // resize both
                if ($widthRatio < $heightRatio) {
                    $newHeight = $height * $widthRatio;
                    $newWidth = $width * $widthRatio;
                } else {
                    $newHeight = $height * $heightRatio;
                    $newWidth = $width * $heightRatio;
                }
            }

            $img->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
            $img->writeImage($path);
        }
    }

    /**
     * @param Message $message
     */
    public function upload(Message $message)
    {
        $picture = $message->getPicture();

        if (!$picture) {
            return;
        }

        if (!is_writable($this->directory)) {
            self::throwPictureHandlerException("Directory '%s' is not writable", $this->directory);
        }

        /** @var UploadedFile $file */
        $file = $picture->getOriginalFilename();
        $fileName = self::generatePictureFilename($message, $file);

        $file->move($this->directory, $fileName);

        $picture->setFilename($this->folder.'/'.$fileName);
        $picture->setOriginalFilename($file->getClientOriginalName());

        $this->resize($picture);
    }

    /**
     * @param Message $message
     * @param UploadedFile $file
     * @return string
     */
    protected static function generatePictureFilename(Message $message, UploadedFile $file)
    {
        $LENGTH = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $string = "";
        $prefix = $message->getId() ? $message->getId()."-" : "";

        for ($i = 0; $i < $LENGTH; $i++) {
            $string .= $characters[rand(0, $charactersLength - 1)];
        }

        return sprintf("%s%s.%s", $prefix, $string, $file->guessExtension());
    }

    /**
     * @param $text
     * @param $path
     * @throws PictureHandlerException
     */
    protected static function throwPictureHandlerException($text, $path)
    {
        throw new PictureHandlerException(sprintf($text, $path));
    }
}
