<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomResource;

class RoomResourceRepository extends AbstractRepository {

    /**
     * Поиск всех ресурсов
     * @return RoomResource[]
     */
    public function findAllResurces() {
        return $this->findAll();
    }

    /**
     * Поиск ресурсов в комнате
     * @param Room $room
     * @return RoomResource[]
     */
    public function findByRoom(Room $room) {
        return $this->findBy(['room' => $room]);
    }
}
