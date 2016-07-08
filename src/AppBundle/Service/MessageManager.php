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
        $this->pictureHandler->uploadPicture($message);
        $this->save($message);
    }

    /**
     * @param Message $message
     */
    public function update(Message $message)
    {
        $token = $this->storage->getToken();

        if (!$token) {
            return;
        }

        /** @var $user User */
        $user = $token->getUser();

        if (!$user || !$user->hasRole("ROLE_ADMIN")) {
            return;
        }

        $uow = $this->em->getUnitOfWork();

        $uow->computeChangeSets();

        $changes = $uow->getEntityChangeSet($message);

        if (count($changes)) {
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
     * @param $message
     */
    protected function save($message)
    {
        $this->em->persist($message);
        $this->em->flush();
    }
}
