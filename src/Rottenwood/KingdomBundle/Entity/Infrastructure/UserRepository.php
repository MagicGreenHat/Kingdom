<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Human;
use Rottenwood\KingdomBundle\Entity\Room;

//TODO[Rottenwood]: Rename to HumanRepository
class UserRepository extends AbstractRepository {

    /**
     * @param int $userId
     * @return Human|null
     */
    public function findById($userId) {
        return $this->find($userId);
    }

    /**
     * @param Room  $room
     * @param array $onlinePlayerIds
     * @param array $excludePlayerIds
     * @return Human[]
     */
    public function findOnlineByRoom($room, array $onlinePlayerIds, array $excludePlayerIds = []) {
        $builder = $this->createQueryBuilder('u');
        $builder->where('u.room = :room');
        $builder->andWhere($builder->expr()->in('u.id', $onlinePlayerIds));

        if (!empty($excludePlayerIds)) {
            $builder->andWhere($builder->expr()->notIn('u.id', $excludePlayerIds));
        }

        $builder->setParameters(['room' => $room]);

        return $builder->getQuery()->getResult();
    }

    /**
     * @return Human[]
     */
    public function findAllUsers() {
        return $this->findAll();
    }

    /**
     * @param string $username
     * @return Human|null
     */
    public function findByUsername($username) {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * @param string $name
     * @return Human|null
     */
    public function findByName($name) {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @param string|int $userNameOrId
     * @return Human|null
     */
    public function findByNameOrId($userNameOrId) {
        return $this->findByName($userNameOrId) ?: $this->findById($userNameOrId);
    }

    /**
     * @param string $email
     * @return Human|null
     */
    public function findByEmail($email) {
        return $this->findOneBy(['email' => $email]);
    }
}
