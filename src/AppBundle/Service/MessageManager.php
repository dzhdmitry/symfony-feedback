<?php

namespace AppBundle\Service;

use AppBundle\Entity\Message;
use Doctrine\ORM\EntityManager;

class MessageManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var PictureHandler
     */
    protected $pictureHandler;

    public function __construct(EntityManager $em, PictureHandler $pictureHandler)
    {
        $this->em = $em;
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
