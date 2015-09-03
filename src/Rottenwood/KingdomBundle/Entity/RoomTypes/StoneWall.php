<?php

namespace Rottenwood\KingdomBundle\Entity\RoomTypes;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Тип комнаты: Замшелая каменная стена
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 */
class StoneWall extends RoomType {

    /** {@inheritdoc} */
    protected $name = 'Замшелая каменная стена';

    /** {@inheritdoc} */
    protected $description = 'Каменная стена служит надежной защитой поселения.';

    /** {@inheritdoc} */
    protected $picture = 'stone-moss';

    /** {@inheritdoc} */
    protected $canWalk = false;
}
