<?php

namespace Rottenwood\KingdomBundle\Entity\RoomTypes;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Тип комнаты: Лес
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 */
class Forest extends RoomType {

    /** {@inheritdoc} */
    protected $name = 'Лес';

    /** {@inheritdoc} */
    protected $description = 'Густой лес раскинул ветви деревьев.';

    /** {@inheritdoc} */
    protected $picture = 'forest';
}
