<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Exception\InvalidCommandParameter;
use Rottenwood\KingdomBundle\Redis\RedisClientInterface;

/**
 * Перемещение по карте
 * Применение в js: Kingdom.Websocket.command('move', 'north|south|west|east')
 */
class Move extends AbstractGameCommand {

    /**
     * @return CommandResponse
     * @throws InvalidCommandParameter
     */
    public function execute() {
        $roomRepository = $this->container->get('kingdom.room_repository');
        $em = $roomRepository->getEntityManager();

        $userId = $this->user->getId();
        $userName = $this->user->getName();

        /** @var Room $currentRoom */
        $currentRoom = $this->user->getRoom();

        $x = $currentRoom->getX();
        $y = $currentRoom->getY();

        if ($this->parameters == 'north') {
            $y++;
            $directionTo = 'на север';
            $directionFrom = 'с юга';
        } elseif ($this->parameters == 'south') {
            $y--;
            $directionTo = 'на юг';
            $directionFrom = 'с севера';
        } elseif ($this->parameters == 'east') {
            $x++;
            $directionTo = 'на восток';
            $directionFrom = 'с запада';
        } elseif ($this->parameters == 'west') {
            $x--;
            $directionTo = 'на запад';
            $directionFrom = 'с востока';
        } else {
            throw new InvalidCommandParameter;
        }

        $destinationRoom = $roomRepository->findOneByXandY($x, $y);

        if (!$destinationRoom || !$this->userCanWalkToRoom($destinationRoom)) {
            $this->result->addError('В эту сторону не пройти');
        } else {
            $this->user->setRoom($destinationRoom);
            $em->flush($this->user);

            /** @var \Redis $redis */
            $redis = $this->container->get('snc_redis.default');
            $redis->hset(RedisClientInterface::ID_ROOM_HASH, $userId, $destinationRoom->getId());

            $logger = $this->container->get('kingdom.logger');
            $logString = sprintf(
                '[%d]%s переместился %s в комнату [%d]%s [%d/%d/%d]',
                $userId,
                $userName,
                $directionTo,
                $destinationRoom->getId(),
                $destinationRoom->getName(),
                $x,
                $y,
                $destinationRoom->getZ()
            );

            $userService = $this->container->get('kingdom.user_service');

            $resultData = [
                'name'          => $userName,
                'directionTo'   => $directionTo,
                'directionFrom' => $directionFrom,
            ];

            if ($usersInCurrentRoom = $userService->getOnlineUsersIdsInRoom($currentRoom, $userId)) {
                $resultData['left'] = $userService->getSessionsByUserIds($usersInCurrentRoom);
            }

            if ($usersInDestinationRoom = $userService->getOnlineUsersIdsInRoom($destinationRoom, $userId)) {
                $resultData['enter'] = $userService->getSessionsByUserIds($usersInDestinationRoom);

                $logString .= sprintf(
                    ', встретил игроков: [%s]',
                    implode(',', $usersInDestinationRoom)
                );
            }

            $logger->info($logString);
            $this->result->setData($resultData);
        }

        return $this->result;
    }

    /**
     * Может ли персонаж войти в комнату
     * @param Room $room
     * @return bool
     */
    private function userCanWalkToRoom(Room $room) {
        return $room->getType()->userCanWalk();
    }
}
