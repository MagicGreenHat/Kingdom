<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Exception\InvalidCommandParameter;

/**
 * Перемещение по карте
 * Применение в js: Kingdom.Websocket.command('move', 'north|south|west|east')
 */
class Move extends AbstractGameCommand {

    const DEFAULT_ROOM = 1;

    /**
     * @return CommandResponse
     * @throws InvalidCommandParameter
     */
    public function execute() {
        $roomRepository = $this->container->get('kingdom.room_repository');
        $em = $roomRepository->getEntityManager();

        //TODO[Rottenwood]: логировать ошибку если у пользователя нет комнаты
        /** @var Room $currentRoom */
        $currentRoom = $this->user->getRoom() ?: $roomRepository->find(self::DEFAULT_ROOM);

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

        if (!$destinationRoom) {
            $this->result->addError('В эту сторону не пройти');
        } else {
            $this->user->setRoom($destinationRoom);
            $em->flush($this->user);

            $userService = $this->container->get('kingdom.user_service');
            $userId = $this->user->getId();

            $resultData = [
                'directionTo'   => $directionTo,
                'directionFrom' => $directionFrom,
                'name'          => $this->user->getName(),
            ];

            if ($usersInCurrentRoom = $userService->getOnlineUsersIdsInRoom($currentRoom, $userId)) {
                $resultData['left'] = $userService->getSessionsByUserIds($usersInCurrentRoom);
            }

            if ($usersInDestinationRoom = $userService->getOnlineUsersIdsInRoom($destinationRoom, $userId)) {
                $resultData['enter'] = $userService->getSessionsByUserIds($usersInDestinationRoom);
            }

            $this->result->setData($resultData);
        }

        return $this->result;
    }
}
