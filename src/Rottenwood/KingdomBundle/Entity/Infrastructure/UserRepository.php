<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\User;

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

    /**
     * @return User[]
     */
    public function findAllUsers() {
        return $this->findAll();
    }

    /**
     * @param string $username
     * @return User
     */
    public function findByUsername($username) {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * @param string $name
     * @return User
     */
    public function findByName($name) {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @param string $email
     * @return User
     */
    public function findOneByEmail($email) {
        return $this->findOneBy(['email' => $email]);
    }
}
