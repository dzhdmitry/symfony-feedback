<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use \Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    /**
     * @param $sort
     * @param $order
     * @param bool $excludeDisapproved
     * @return Message[]
     */
    public function findMessages($sort, $order, $excludeDisapproved = true)
    {
        $qb = $this->createQueryBuilder("message")
            ->addSelect("picture")
            ->leftJoin("message.picture", "picture")
            ->orderBy("message.".$sort, $order);

        if ($excludeDisapproved) {
            $qb->where("message.approved = :approved")
                ->setParameter("approved", true);
        }

        $messages = $qb->getQuery()->getResult();

        return $messages;
    }
}
