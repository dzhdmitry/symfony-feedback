<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="message.author")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="message.email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="message.body")
     */
    private $body;

    /**
     * @ORM\Column(type="boolean")
     */
    private $approved;

    /**
     * @ORM\Column(type="boolean")
     */
    private $changedByAdmin;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Picture", mappedBy="message", cascade={"persist"})
     */
    private $picture;

    public function __construct()
    {
        $this->approved = false;
        $this->changedByAdmin = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param boolean $approved
     * @return $this
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $changedByAdmin
     * @return $this
     */
    public function setChangedByAdmin($changedByAdmin)
    {
        $this->changedByAdmin = $changedByAdmin;

        return $this;
    }

    /**
     * @return bool
     */
    public function isChangedByAdmin()
    {
        return $this->changedByAdmin;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Picture $picture
     * @return Message
     */
    public function setPicture(Picture $picture = null)
    {
        if ($picture) {
            $this->picture = $picture->setMessage($this);
        }

        return $this;
    }

    /**
     * @return Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
