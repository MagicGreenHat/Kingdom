<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Inventory;
use Rottenwood\KingdomBundle\Entity\User;

class InventoryRepository extends AbstractRepository {

    /**
     * @param User $user
     * @return Inventory
     */
    public function findByUser(User $user) {
        return $this->findBy(['user' => $user]);
    }
}
