<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Human;
use Rottenwood\KingdomBundle\Entity\Money;

class MoneyRepository extends AbstractRepository {

    /**
     * @param Human|int $userId
     * @return Money|null
     */
    public function findOneByUser($userId) {
        return $this->findOneBy(['user' => $userId]);
    }
}
