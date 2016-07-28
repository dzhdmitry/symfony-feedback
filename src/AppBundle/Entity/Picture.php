<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="picture", indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"slug"})
 */
class Picture
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="slug", type="string", unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(name="originalFilename", type="string", length=255)
     */
    private $originalFilename;

    /**
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Message", inversedBy="picture")
     * @ORM\JoinColumn(nullable=false)
     */
    private $message;

    public function __construct()
    {
        $this->slug = $this->generateUniqueRandomString();
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateUniqueRandomString($length = 10)
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Picture
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set originalFilename
     *
     * @param UploadedFile|string $originalFilename
     * @return Picture
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    /**
     * Get originalFilename
     *
     * @return UploadedFile|string
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Picture
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Picture
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set message
     *
     * @param Message $message
     * @return Picture
     */
    public function setMessage(Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getAbsolutePath()
    {
        return __DIR__."/../../../var/" . $this->getPath() . "/" . $this->getFilename();
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
