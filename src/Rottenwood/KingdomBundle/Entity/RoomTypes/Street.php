<?php

namespace Rottenwood\KingdomBundle\Entity\RoomTypes;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Тип комнаты: Улица
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 */
class Street extends RoomType
{

    /** {@inheritdoc} */
    protected $name = 'Городская улица';

    /** {@inheritdoc} */
    protected $description = 'Городская улица, вымощенная крупным булыжником.';

    /** {@inheritdoc} */
    protected $picture = 'street';
}
