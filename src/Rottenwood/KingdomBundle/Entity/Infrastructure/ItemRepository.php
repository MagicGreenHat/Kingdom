<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

class ItemRepository extends AbstractRepository {

    /**
     * @param int $userId
     * @return Item
     */
    public function findById($userId) {
        return $this->find($userId);
    }
}
