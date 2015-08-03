<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomRepository;

class Move extends AbstractGameCommand {

    /**
     * @return string
     */
    public function execute() {
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->entityManager->getRepository(Room::class);

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
        }

        $destinationRoom = $roomRepository->findOneByXandY($x, $y);

        $result = new CommandResponse();

        if (!$destinationRoom) {
            $result->addError('В эту сторону не пройти');
        } else {
            $roomType = $destinationRoom->getType();

            $result->setResult([
                'name'    => $destinationRoom->getName(),
                'type'    => $roomType->getName(),
                'picture' => $roomType->getPicture(),
            ]);

            $this->user->setRoom($destinationRoom);
            $this->entityManager->flush($this->user);
        }

        return $result->result();
    }
}
