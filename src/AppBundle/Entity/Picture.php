<?php

namespace AppBundle\Entity;

use AppBundle\Util\StringHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"slug"})
 */
class Picture
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originalFilename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Message", inversedBy="picture")
     * @ORM\JoinColumn(nullable=false)
     */
    private $message;

    public function __construct()
    {
        $this->slug = StringHelper::uniqueRandomString();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $slug
     * @return Picture
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param UploadedFile|string $originalFilename
     * @return Picture
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    /**
     * @return UploadedFile|string
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * @param string $path
     * @return Picture
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $filename
     * @return Picture
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param Message $message
     * @return Picture
     */
    public function setMessage(Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return __DIR__ . "/../../../web/" . $this->getPath() . "/" . $this->getFilename();
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return $this->getPath() . "/" . $this->getFilename();
    }

    /**
     * @ORM\PostRemove
     */
    public function removeFile()
    {
        $path = $this->getAbsolutePath();

        if (!is_file($path)) {
            return;
        }

        if (!is_writable($path)) {
            return;
        }

        @unlink($path);
    }
}
