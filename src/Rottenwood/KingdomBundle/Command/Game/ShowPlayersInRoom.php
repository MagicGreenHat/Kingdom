<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\User;
use Rottenwood\KingdomBundle\Redis\RedisClientInterface;

/**
 * Отображение игроков в текущей комнате
 * @package Rottenwood\KingdomBundle\Command\Game
 */
class ShowPlayersInRoom extends AbstractGameCommand {

    /**
     * @return CommandResponse
     */
    public function execute() {
        /** @var RedisClientInterface $redis */
        $redis = $this->container->get('snc_redis.default');

        $onlinePlayersIds = array_map(
            function ($player) {
                return json_decode($player, true)['id'];
            },
            $redis->hgetall(RedisClientInterface::CHARACTERS_HASH_NAME)
        );

        $playersInRoom = array_map(
            function (User $user) {
                return $user->getUsername() . ' стоит тут.';
            },
            $this->container->get('kingdom.user_repository')->findOnlineByRoom(
                $this->user->getRoom(),
                $onlinePlayersIds,
                [$this->user->getId()]
            )
        );

        $response = new CommandResponse('showPlayersInRoom');
        $response->setData($playersInRoom);

        return $response;
    }
}
