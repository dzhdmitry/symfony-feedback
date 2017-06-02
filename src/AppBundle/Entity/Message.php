<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\NotBlank(message="message.author")
     */
    private $author;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\NotBlank(message="message.email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="body", type="text")
     * @Assert\NotBlank(message="message.body")
     */
    private $body;

    /**
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;

    /**
     * @ORM\Column(name="changed_by_admin", type="boolean")
     */
    private $changedByAdmin;

    /**
     * @ORM\Column(name="createdAt", type="datetime")
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     * @return $this
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * Set changedByAdmin
     *
     * @param boolean $changedByAdmin
     * @return $this
     */
    public function setChangedByAdmin($changedByAdmin)
    {
        $this->changedByAdmin = $changedByAdmin;

        return $this;
    }

    /**
     * Get changedByAdmin
     *
     * @return bool
     */
    public function isChangedByAdmin()
    {
        return $this->changedByAdmin;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set picture
     *
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
     * Get picture
     *
     * @return Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
