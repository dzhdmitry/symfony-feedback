<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use AppBundle\Util\MessagesCriteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;

class MessageRepository extends EntityRepository
{
    /**
     * @param MessagesCriteria $criteria
     * @return Message[]
     */
    public function findMessages(MessagesCriteria $criteria)
    {
        $qb = $this->createQueryBuilder('message')
            ->addSelect('picture')
            ->leftJoin('message.picture', 'picture')
            ->orderBy('message.'.$criteria->sort, $criteria->direction);

        if ($criteria->approved === MessagesCriteria::FILTER_APPROVED) {
            $qb->where('message.approved = :approved')
                ->setParameter('approved', true);
        } elseif ($criteria->approved === MessagesCriteria::FILTER_DISAPPROVED) {
            $qb->where('message.approved = :approved')
                ->setParameter('approved', false);
        }

        $messages = $qb->getQuery()->getResult();

        return $messages;
    }

    /**
     * @param string $approved
     * @return int
     */
    public function countMessages($approved = MessagesCriteria::FILTER_ALL)
    {
        $qb = $this->createQueryBuilder('message')
            ->select('COUNT(message)');

        if ($approved === MessagesCriteria::FILTER_APPROVED) {
            $qb->where('message.approved = :approved')
                ->setParameter('approved', true);
        } elseif ($approved === MessagesCriteria::FILTER_DISAPPROVED) {
            $qb->where('message.approved = :approved')
                ->setParameter('approved', false);
        }

        try {
            $messages = (int)$qb->getQuery()->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            $messages = 0;
        }

        return $messages;
    }
}
