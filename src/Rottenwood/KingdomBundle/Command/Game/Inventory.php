<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;

/**
 * Просмотр инвентаря и одетых вещей игрока
 * Применение в js: Kingdom.Websocket.command('inventory')
 */
class Inventory extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $equipedItemsIds = $this->container->get('kingdom.user_service')->getEquipedItemsIds($this->user);
        $inventoryItems = $this->container->get('kingdom.inventory_item_repository')->findByUser($this->user);

        $itemData = [];
        foreach ($inventoryItems as $inventoryItem) {
            $item = $inventoryItem->getItem();
            $itemId = $item->getId();

            $itemResult = [
                'itemId'       => $itemId,
                'name'         => $item->getName(),
                'description'  => $item->getDescription(),
                'quantity'     => $inventoryItem->getQuantity(),
                'allowedSlots' => $item->getSlots(),
                'pic'          => $item->getPicture(),
            ];

            if (in_array($itemId, $equipedItemsIds)) {
                $itemResult['slot'] = array_search($itemId, $equipedItemsIds);
            }

            $itemData[] = $itemResult;
        }

        $this->result->setData($itemData);

        return $this->result;
    }
}
