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
    public function execute() {
        $parameters = explode(':', $this->parameters);
        $itemId = $parameters[0];
        $slot = $parameters[1];

        $inventoryItemRepository = $this->container->get('kingdom.inventory_item_repository');
        $item = $inventoryItemRepository->findOneByUserAndItemId($this->user, $itemId);

        // Проверка на то, подходит ли предмет к слоту
        if (in_array($slot, $item->getItem()->getSlots())) {
            $item->setSlot($slot);

            $inventoryItemRepository->flush();
        } else {
            throw new WrongSlot($slot);
        }


        return $this->result;
    }
}
