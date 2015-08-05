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
     * @param array $onlinePlayerIds
     * @param array $excludePlayerIds
     * @return User[]
     */
    public function findOnlineByRoom($room, array $onlinePlayerIds, array $excludePlayerIds = []) {
        $builder = $this->createQueryBuilder('u');
        $builder->where('u.room = :room');
        $builder->andWhere($builder->expr()->in('u.id', $onlinePlayerIds));

        if ($excludePlayerIds) {
            $builder->andWhere($builder->expr()->notIn('u.id', $excludePlayerIds));
        }

        $builder->setParameters(['room' => $room]);

        return $builder->getQuery()->getResult();
    }
}
