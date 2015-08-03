<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomRepository;
use Rottenwood\KingdomBundle\Exception\CommandParameterException;

class Move extends AbstractGameCommand {

    /**
     * @return string
     * @throws CommandParameterException
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
        } else {
            throw new CommandParameterException;
        }

        $destinationRoom = $roomRepository->findOneByXandY($x, $y);

        if (!$destinationRoom) {
            $this->response->addError('В эту сторону не пройти');
        } else {
            $roomType = $destinationRoom->getType();

            $this->response->setData([
                'name'    => $destinationRoom->getName(),
                'type'    => $roomType->getName(),
                'picture' => $roomType->getPicture(),
            ]);

            $this->user->setRoom($destinationRoom);
            $this->entityManager->flush($this->user);
        }

        return $this->response->result();
    }
}
