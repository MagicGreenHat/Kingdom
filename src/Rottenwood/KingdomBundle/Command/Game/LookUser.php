<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Exception\UserNotFound;

/**
 * Просмотр информации и одетых вещей другого игрока
 * Применение в js: Kingdom.Websocket.command('lookUser', 'ИмяПерсонажа')
 */
class LookUser extends AbstractGameCommand {

    /**
     * @return CommandResponse
     * @throws UserNotFound
     */
    public function execute(): CommandResponse {
        $userToLookAt = $this->container->get('kingdom.human_repository')->findByName($this->parameters);

        if (!$userToLookAt) {
            throw new UserNotFound;
        }

        $itemData = [];
        foreach ($this->container->get('kingdom.inventory_item_repository')->findByUser($userToLookAt) as $inventoryItem) {
            $item = $inventoryItem->getItem();

            $itemData[] = [
                'name'        => $item->getName(),
                'description' => $item->getDescription(),
                'slots'       => $item->getSlots(),
                'pic'         => $item->getPicture(),
                'slot'        => $inventoryItem->getSlot(),
            ];
        }

        $this->result->setData(
            [
                'name'   => $userToLookAt->getName(),
                'items'  => $itemData,
                'avatar' => $userToLookAt->getAvatar(),
            ]
        );

        return $this->result;
    }
}
