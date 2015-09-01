<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;

/**
 * Снять предмет
 * Список слотов доступен в статическом методе Item::getAllSlotNames()
 * Применение в js: Kingdom.Websocket.command('remove', 'названиеСлота')
 */
class Remove extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $inventoryItemRepository = $this->container->get('kingdom.inventory_item_repository');
        $item = $inventoryItemRepository->findOneByUserAndSlot($this->user, $this->parameters);
        $item->removeSlot();

        $inventoryItemRepository->flush();

        $this->result->setData(['slot' => $this->parameters]);

        return $this->result;
    }
}
