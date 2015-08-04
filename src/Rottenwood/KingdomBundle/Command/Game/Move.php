<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Exception\InvalidCommandParameter;

class Move extends AbstractGameCommand {

    /**
     * @return CommandResponse
     * @throws InvalidCommandParameter
     */
    public function execute() {
        $roomRepository = $this->container->get('kingdom.room_repository');
        $em = $roomRepository->getEntityManager();

        //TODO[Rottenwood]: логировать ошибку если у пользователя нет комнаты
        $currentRoom = $this->user->getRoom() ?: $roomRepository->find(1);

        $x = $currentRoom->getX();
        $y = $currentRoom->getY();

        if ($this->parameters == 'north') {
            $y++;
        } elseif ($this->parameters == 'south') {
            $y--;
        } elseif ($this->parameters == 'east') {
            $x++;
        } elseif ($this->parameters == 'west') {
            $x--;
        } else {
            throw new InvalidCommandParameter;
        }

        $destinationRoom = $roomRepository->findOneByXandY($x, $y);

        $result = new CommandResponse('move');

        if (!$destinationRoom) {
            $result->addError('В эту сторону не пройти');
        } else {
            $roomType = $destinationRoom->getType();

            $result->setData([
                'name'        => $destinationRoom->getName() ?: $roomType->getName(),
                'description' => $destinationRoom->getDescription() ?: $roomType->getDescription(),
                'x'           => $x,
                'y'           => $y,
            ]);

            $this->user->setRoom($destinationRoom);
            $em->flush($this->user);
        }

        return $result;
    }
}
