<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Money;
use Rottenwood\KingdomBundle\Entity\User;

class MoneyRepository extends AbstractRepository {

    /**
     * @param User|int $userId
     * @return Money|null
     */
    public function findOneByUser($userId) {
        return $this->findOneBy(['user' => $userId]);
    }
}
