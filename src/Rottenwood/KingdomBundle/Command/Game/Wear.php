<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Infrastructure\Item;

/**
 * Одеть предмет
 * Список слотов доступен в статическом методе Item::getAllSlotNames()
 * Применение в js: Kingdom.Websocket.command('wear', ['idПредмета', 'названиеСлота'])
 */
class Wear extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $parameters = explode(':', $this->parameters);
        $itemId = $parameters[0];
        $slot = $parameters[1];

        $inventoryItemRepository = $this->container->get('kingdom.inventory_item_repository');
        $item = $inventoryItemRepository->findOneByUserAndItemId($this->user, $itemId);

        //TODO[Rottenwood]: Проверка на то, подходит ли предмет к слоту
        $item->setSlot($slot);

        $inventoryItemRepository->flush();

        return $this->result;
    }
}
