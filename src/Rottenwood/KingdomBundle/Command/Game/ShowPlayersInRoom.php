<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\User;

/**
 * Отображение игроков в текущей комнате
 * @package Rottenwood\KingdomBundle\Command\Game
 */
class ShowPlayersInRoom extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        $playersInRoom = array_map(
            function (User $user) {
                return $user->getName() . ' стоит тут.';
            },
            $this->container->get('kingdom.user_service')->getOnlineUsersInRoom(
                $this->user->getRoom(),
                $this->user->getId()
            )
        );

        $response = new CommandResponse('showPlayersInRoom');
        $response->setData($playersInRoom);

        return $response;
    }
}
