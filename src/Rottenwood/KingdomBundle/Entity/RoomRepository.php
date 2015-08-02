<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RoomRepository extends EntityRepository {

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
