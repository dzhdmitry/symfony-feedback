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

    const TYPE_PREVIEW = "/previews/";
    const TYPE_PICTURE = "/pictures/";

    protected $previewsDir;

    protected $picturesDir;

    public function __construct($previewsDir, $picturesDir)
    {
        $this->previewsDir = $previewsDir;
        $this->picturesDir = $picturesDir;
    }

    /**
     * @param Picture $picture
     */
    public function resizePicture(Picture $picture)
    {
        $path = $picture->getAbsolutePath();

        if (!is_file($path)) {
            $this->throwPictureHandlerException("File '%s' does not exist", $path);
        }

        if (!is_writable($picture->getAbsolutePath())) {
            $this->throwPictureHandlerException("File '%s' is not writable", $path);
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
    public function uploadPicture(Message $message)
    {
        $this->upload($message, self::TYPE_PICTURE);
    }

    /**
     * @param Message $message
     */
    public function uploadPreview(Message $message)
    {
        $this->upload($message, self::TYPE_PREVIEW);
    }

    /**
     * @param Message $message
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
     * @param Message $message
     * @param $type
     */
    protected function upload(Message $message, $type)
    {
        $picture = $message->getPicture();
        $destination = ($type == self::TYPE_PICTURE) ? $this->picturesDir : $this->previewsDir;

        if (!$picture) {
            return;
        }

        if (!is_writable($destination)) {
            $this->throwPictureHandlerException("Directory '%s' is not writable", $destination);//todo
        }

        /** @var UploadedFile $file */
        $file = $picture->getOriginalFilename();
        $fileName = self::generatePictureFilename($message, $file);

        $file->move($destination, $fileName);

        $picture->setFilename($type.$fileName);
        $picture->setOriginalFilename($file->getClientOriginalName());

        $this->resizePicture($picture);
    }

    /**
     * @param $text
     * @param $path
     * @throws PictureHandlerException
     */
    protected function throwPictureHandlerException($text, $path)
    {
        throw new PictureHandlerException(sprintf($text, $path));
    }
}
