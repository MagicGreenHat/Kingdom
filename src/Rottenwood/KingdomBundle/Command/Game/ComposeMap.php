<?php

namespace Rottenwood\KingdomBundle\Command\Game;

use Rottenwood\KingdomBundle\Command\Infrastructure\AbstractGameCommand;
use Rottenwood\KingdomBundle\Command\Infrastructure\CommandResponse;
use Rottenwood\KingdomBundle\Entity\Room;

class ComposeMap extends AbstractGameCommand {

    /**
     * Количество комнат от центра в каждую сторону
     * @var int
     */
    private $mapRadius = 2;

    /**
     * @return CommandResponse
     */
    public function execute() {
        $currentRoom = $this->user->getRoom();
        $currentX = $currentRoom->getX();
        $currentY = $currentRoom->getY();

        $roomRepository = $this->container->get('kingdom.room_repository');

        $map = [];

        for (
            $y = $currentY - $this->mapRadius, $relativeY = 1, $relativeX = 1;
            $y <= $currentY + $this->mapRadius;
            $y++, $relativeY++, $relativeX = 1
        ) {
            for ($x = $currentX - $this->mapRadius; $x <= $currentX + $this->mapRadius; $x++, $relativeX++) {
                // пропуск центра карты, комната где находится персонаж
                if ($relativeY == 3 && $relativeX == 3) {
                	continue;
                }

                $room = $roomRepository->findOneByXandY($x, $y);

                if ($room) {
                    $map[] = $this->addRoom($room, $relativeX, $relativeY);
                }
            }
        }

        return new CommandResponse([], $map);
    }

    /**
     * @param Room $room
     * @param int  $x
     * @param int  $y
     * @return array
     */
    private function addRoom(Room $room, $x, $y) {
        return [
            'x'   => $x,
            'y'   => $y,
            'pic' => $room->getType()->getPicture(),
        ];
    }
}
