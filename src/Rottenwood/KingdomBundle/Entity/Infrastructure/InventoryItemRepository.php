<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\User;

class InventoryItemRepository extends AbstractRepository {

    /**
     * @param User $user
     * @return InventoryItem
     */
    public function findByUser(User $user) {
        return $this->findBy(['user' => $user]);
    }
}
