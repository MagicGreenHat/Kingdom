<?php

namespace Rottenwood\KingdomBundle\Entity;

use Rottenwood\KingdomBundle\Entity\Infrastructure\AbstractRepository;

class UserRepository extends AbstractRepository {

    /**
     * @param int $userId
     * @return User
     */
    public function findById($userId) {
        return $this->find($userId);
    }

    /**
     * @param Room  $room
     * @param int[] $onlinePlayersIds
     * @return User[]
     */
    public function findOnlineByRoom($room, array $onlinePlayersIds) {
        $builder = $this->createQueryBuilder('u');
        $builder->where('u.room = :room');
        $builder->andWhere($builder->expr()->in('u.id', $onlinePlayersIds));
        $builder->setParameters(['room' => $room]);

        return $builder->getQuery()->getResult();
    }
}
