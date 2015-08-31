<?php

namespace Rottenwood\KingdomBundle\Entity\RoomTypes;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Тип комнаты: Дорога
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomTypeRepository")
 */
class Road extends RoomType {

    /** {@inheritdoc} */
    protected $name = 'Дорога';

    /** {@inheritdoc} */
    protected $description = 'Пыльная лесная дорога проходит тут.';

    /** {@inheritdoc} */
    protected $picture = 'road';
}
