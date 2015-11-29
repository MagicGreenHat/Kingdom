<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\InventoryItem;

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
     * @param User   $user
     * @param string $itemId
     * @return InventoryItem|null
     */
    public function findOneByUserAndItemId(User $user, $itemId) {
        return $this->findOneBy(
            [
                'user' => $user,
                'item' => $itemId
            ]
        );
    }

    /**
     * Поиск предмета по игроку и слоту
     * @param User $user
     * @param string $slotName
     * @return InventoryItem|null
     */
    public function findOneByUserAndSlot($user, $slotName) {
        $builder = $this->createQueryBuilder('inventory_item');
        $builder->select('inventory_item');
        $builder->where('inventory_item.user = :user');
        $builder->andWhere('inventory_item.slot = :slotName');
        $builder->setParameters(
            [
                'user'     => $user,
                'slotName' => $slotName,
            ]
        );

        return $builder->getQuery()->getOneOrNullResult();
    }
}
