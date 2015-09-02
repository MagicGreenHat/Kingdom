<?php

namespace Rottenwood\KingdomBundle\Entity\RoomTypes;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Тип комнаты: Ворота
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 */
class Gate extends RoomType {

    /** {@inheritdoc} */
    protected $name = 'Городские ворота';

    /** {@inheritdoc} */
    protected $description = 'Тяжелые городские ворота охраняются стражей.';

    /** {@inheritdoc} */
    protected $picture = 'gate';
}
