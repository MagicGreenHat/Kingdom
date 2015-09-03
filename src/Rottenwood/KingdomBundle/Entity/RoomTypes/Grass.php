<?php

namespace Rottenwood\KingdomBundle\Entity\RoomTypes;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Тип комнаты: Травяной луг
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 */
class Grass extends RoomType {

    /** {@inheritdoc} */
    protected $name = 'Травяной луг';

    /** {@inheritdoc} */
    protected $description = 'Запах трав наполняет пространство этого открытого зеленого участка.';

    /** {@inheritdoc} */
    protected $picture = 'grass';
}
