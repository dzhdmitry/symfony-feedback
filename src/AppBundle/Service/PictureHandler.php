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

    public function __construct($uploadsDir, $folder)
    {
        $this->directory = $uploadsDir . $folder;
        $this->folder = $folder;
    }

    /**
     * @param Picture $picture
     * @throws PictureHandlerException
     */
    public function resize(Picture $picture)
    {
        $path = $picture->getAbsolutePath();

        if (!is_file($path)) {
            throw PictureHandlerException::create('File "%s" does not exist', $path);
        }

        if (!is_writable($picture->getAbsolutePath())) {
            throw PictureHandlerException::create('File "%s" is not writable', $path);
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
     * @throws PictureHandlerException
     */
    public function upload(Message $message)
    {
        $picture = $message->getPicture();

        if (!$picture) {
            return;
        }

        if (!is_writable($this->directory)) {
            throw PictureHandlerException::create('Directory "%s" is not writable', $this->directory);
        }

        /** @var UploadedFile $file */
        $file = $picture->getOriginalFilename();
        $fileName = sprintf('%s.%s', $picture->getSlug(), $file->guessExtension());

        $file->move($this->directory, $fileName);

        $picture->setFilename($fileName);
        $picture->setOriginalFilename($file->getClientOriginalName());
        $picture->setPath($this->folder);

        $this->resize($picture);
    }
}
