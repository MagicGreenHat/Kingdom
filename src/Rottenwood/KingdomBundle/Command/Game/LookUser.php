<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Exception\UserNotFound;

/**
 * Просмотр информации и одетых вещей другого игрока
 */
class LookUser extends AbstractGameCommand {

    /**
     * @return CommandResponse
     * @throws UserNotFound
     */
    public function execute() {
        $result = new CommandResponse('lookUser');

        $userToLookAt = $this->container->get('kingdom.user_repository')->findById($this->parameters);

        if (!$userToLookAt) {
            throw new UserNotFound;
        }

        $itemData = [];
        foreach ($userToLookAt->getEquipment() as $slot => $item) {
            $itemData[] = [
                'name'        => $item->getName(),
                'description' => $item->getDescription(),
                'slots'       => $item->getSlots(),
                'pic'         => $item->getPicture(),
                'slot'        => $slot,
            ];
        }

        $result->setData(
            [
                'name'  => $userToLookAt->getUsername(),
                'items' => $itemData,
            ]
        );

        return $result;
    }
}
