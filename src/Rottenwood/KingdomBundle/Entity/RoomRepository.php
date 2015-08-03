<?php

namespace Rottenwood\KingdomBundle\Entity;

use Rottenwood\KingdomBundle\Entity\Infrastructure\AbstractRepository;

class RoomRepository extends AbstractRepository {

    /**
     * Поиск по координатам
     * @param int $x
     * @param int $y
     * @return Room
     */
    public function findOneByXandY($x, $y) {
        return $this->findOneBy(['x' => $x, 'y' => $y]);
    }
}
