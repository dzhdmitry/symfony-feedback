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
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Message $message)
    {
        $this->pictureHandler->upload($message);
        $this->em->persist($message);
        $this->em->flush();
    }

    /**
     * @param Message $message
     * @param array $data
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Message $message, $data = [])
    {
        if ($this->changedByAdmin($message, $data)) {
            $message->setChangedByAdmin(true);
        }

        $this->em->flush();
    }

    /**
     * @param Message $message
     * @param $approved
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setApproved(Message $message, $approved)
    {
        $message->setApproved($approved);
        $this->em->flush();
    }

    /**
     * @param Message $message
     * @param array $data
     * @return bool
     */
    private function changedByAdmin(Message $message, $data = [])
    {
        if (array_key_exists('body', $data)) {
            return false;
        }

        if ($data['body'] === $message->getBody()) {
            return false;
        }

        if ($token = $this->storage->getToken()) {
            if ($user = $token->getUser()) {
                /** @var $user User */
                if ($user->hasRole('ROLE_ADMIN')) {
                    return true;
                }
            }
        }

        return false;
    }
}
