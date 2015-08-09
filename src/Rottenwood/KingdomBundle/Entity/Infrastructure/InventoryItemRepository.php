<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\InventoryItem;
use Rottenwood\KingdomBundle\Entity\User;

class InventoryItemRepository extends AbstractRepository {

    /**
     * Все предметы в инвентаре игрока
     * @param User $user
     * @return InventoryItem[]
     */
    public function findByUser(User $user) {
        return $this->findBy(['user' => $user]);
    }

    /**
     * Предмет с itemId в инвентаре игрока
     * @param User $user
     * @param int  $itemId
     * @return InventoryItem
     */
    public function findOneByUserAndItemId(User $user, $itemId) {
        $builder = $this->createQueryBuilder('inventory_item');
        $builder->select('inventory_item');
        $builder->where('inventory_item.user = :user');
        $builder->andWhere('inventory_item.item = :itemId');
        $builder->setParameters(
            [
                'user'   => $user,
                'itemId' => $itemId,
            ]
        );

        return $builder->getQuery()->getSingleResult();
    }
}
