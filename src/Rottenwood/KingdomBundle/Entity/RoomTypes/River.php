<?php

namespace Rottenwood\KingdomBundle\Entity\RoomTypes;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Тип комнаты: Река
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 */
class River extends RoomType {

    /** {@inheritdoc} */
    protected $name = 'Река';

    /** {@inheritdoc} */
    protected $description = 'Широкое мелкое русло с мутной водой заросло травой и водорослями.
        То и дело на пути попадаются небольшие, но заросшие непроходимым
        лесом острова. Гигантские деревья нависают над головой, погружая
        реку в полумрак.';

    /** {@inheritdoc} */
    protected $picture = 'river';
}
