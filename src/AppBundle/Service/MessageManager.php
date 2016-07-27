<?php

namespace AppBundle\Service;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MessageManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    protected $storage;

    /**
     * @var PictureHandler
     */
    protected $pictureHandler;

    public function __construct(EntityManager $em, TokenStorageInterface $storage, PictureHandler $pictureHandler)
    {
        $this->em = $em;
        $this->storage = $storage;
        $this->pictureHandler = $pictureHandler;
    }

    /**
     * @param Message $message
     */
    public function create(Message $message)
    {
        $this->pictureHandler->upload($message);
        $this->save($message);
    }

    /**
     * @param Message $message
     * @param array $data
     */
    public function update(Message $message, $data = [])
    {
        if ($this->userIsAdmin() && self::hasChanged($message, $data)) {
            $message->setChangedByAdmin(true);
        }

        $this->save($message);
    }

    /**
     * @param Message $message
     * @param $approved
     */
    public function setApproved(Message $message, $approved)
    {
        $message->setApproved($approved);
        $this->save($message);
    }

    /**
     * @param Message $message
     * @param array $data
     * @return bool
     */
    protected static function hasChanged(Message $message, $data = [])
    {
        $changed = false;

        if (array_key_exists("body", $data)) {
            if ($data["body"] !== $message->getBody()) {
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * @return bool
     */
    protected function userIsAdmin()
    {
        if ($token = $this->storage->getToken()) {
            if ($user = $token->getUser()) {
                /** @var $user User */
                if ($user->hasRole("ROLE_ADMIN")) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $message
     */
    protected function save($message)
    {
        $this->em->persist($message);
        $this->em->flush();
    }
}
