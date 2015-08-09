<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;

/**
 * Просмотр инвентаря и одетых вещей игрока
 */
class Inventory extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $result = new CommandResponse('inventory');

        $equipedItemsIds = $this->container->get('kingdom.user_service')->getEquipedItemsIds($this->user);
        $inventoryItems = $this->container->get('kingdom.inventory_item_repository')->findByUser($this->user);

        $itemData = [];
        foreach ($inventoryItems as $inventoryItem) {
            $item = $inventoryItem->getItem();
            $itemId = $item->getId();

            $itemData[] = [
                'itemId' => $itemId,
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'slots' => $item->getSlots(),
                'pic' => $item->getPicture(),
                'worn' => in_array($itemId, $equipedItemsIds),
            ];
        }

        $result->setData($itemData);

        return $result;
    }
}
