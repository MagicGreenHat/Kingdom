<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Exception\WrongSlot;

/**
 * Одеть предмет
 * Список слотов доступен в статическом методе Item::getAllSlotNames()
 * Применение в js: Kingdom.Websocket.command('wear', ['idПредмета', 'названиеСлота'])
 */
class Wear extends AbstractGameCommand {

    /**
     * @return CommandResponse
     * @throws WrongSlot
     */
    public function execute(): CommandResponse {
        $parameters = explode(':', $this->parameters);
        $itemId = $parameters[0];
        $slot = $parameters[1];

        $inventoryItemRepository = $this->container->get('kingdom.inventory_item_repository');
        $inventoryItem = $inventoryItemRepository->findOneByUserAndItemId($this->user, $itemId);

        if (!$inventoryItem->getItem()->fitsTo($slot)) {
            throw new WrongSlot($slot);
        }

        $wornItem = $inventoryItemRepository->findOneByUserAndSlot($this->user, $slot);

        if ($wornItem) {
            $wornItem->removeSlot();
            $inventoryItemRepository->flush($wornItem);
        }

        $inventoryItem->setSlot($slot);

        $inventoryItemRepository->flush($inventoryItem);

        return $this->result;
    }
}
