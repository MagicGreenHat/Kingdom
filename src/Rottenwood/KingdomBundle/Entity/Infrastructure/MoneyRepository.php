<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Money;

class MoneyRepository extends AbstractRepository {

    /**
     * @param int $userId User object or id int
     * @return Money
     */
    public function findOneByUser($userId) {
        return $this->findOneBy(['user' => $userId]);
    }
}
