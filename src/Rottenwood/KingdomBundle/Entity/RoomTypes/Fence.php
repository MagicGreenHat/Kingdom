<?php

namespace Rottenwood\KingdomBundle\Entity\RoomTypes;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Тип комнаты: Частокол
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 */
class Fence extends RoomType {

    /** {@inheritdoc} */
    protected $name = 'Частокол';

    /** {@inheritdoc} */
    protected $description = 'Забор из остро заточенных бревен, поставленных стеной.';

    /** {@inheritdoc} */
    protected $picture = 'fence';
}
