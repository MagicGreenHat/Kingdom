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
    public function execute(): CommandResponse {
        $inventoryItems = $this->container->get('kingdom.inventory_item_repository')->findByUser($this->user);

        $itemData = [];
        foreach ($inventoryItems as $inventoryItem) {
            $item = $inventoryItem->getItem();
            $itemId = $item->getId();
            $itemSlot = $inventoryItem->getSlot();

            $itemResult = [
                'itemId'       => $itemId,
                'name'         => $item->getName(),
                'name4'        => $item->getName4(),
                'description'  => $item->getDescription(),
                'quantity'     => $inventoryItem->getQuantity(),
                'allowedSlots' => $item->getSlots(),
                'pic'          => $item->getPicture(),
            ];

            if ($itemSlot) {
                $itemResult['slot'] = $itemSlot;
            }

            $itemData[] = $itemResult;
        }

        $this->result->setData($itemData);

        return $this->result;
    }
}
