<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomRepository;

class Move extends AbstractGameCommand {

    /**
     * @return string
     */
    public function execute() {
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->entityManager->getRepository(Room::class);

        $currentRoom = $this->user->getRoom();

        //TODO[Rottenwood]: Исправить проверку, логировать ошибку если у пользователя нет комнаты
        if (!$currentRoom) {
        	$currentRoom = $roomRepository->find(25);
        }

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

        $result = [];

        if (!$destinationRoom) {
            $result['error'] = 'В эту сторону не пройти';
        } else {
            $roomType = $destinationRoom->getType();
            $result = [
                'name'    => $destinationRoom->getName(),
                'type'    => $roomType->getName(),
                'picture' => $roomType->getPicture(),
            ];
        }

        $this->user->setRoom($destinationRoom);

        return $result;
    }
}
