<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use \Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    /**
     * @param $sort
     * @param $order
     * @param bool|null $approved
     * @return Message[]
     */
    public function findMessages($sort, $order, $approved = null)
    {
        $qb = $this->createQueryBuilder("message")
            ->addSelect("picture")
            ->leftJoin("message.picture", "picture")
            ->orderBy("message.".$sort, $order);

        if ($approved === true) {
            $qb->where("message.approved = :approved")
                ->setParameter("approved", true);
        } elseif ($approved === false) {
            $qb->where("message.approved = :approved")
                ->setParameter("approved", false);
        }

        $messages = $qb->getQuery()->getResult();

        return $messages;
    }

    /**
     * @param bool|null $approved
     * @return int
     */
    public function countMessages($approved = null)
    {
        $qb = $this->createQueryBuilder("message")
            ->select("COUNT(message)");

        if ($approved === true) {
            $qb->where("message.approved = :approved")
                ->setParameter("approved", true);
        } elseif ($approved === false) {
            $qb->where("message.approved = :approved")
                ->setParameter("approved", false);
        }

        $messages = $qb->getQuery()->getSingleScalarResult();

        return (int)$messages;
    }
}
