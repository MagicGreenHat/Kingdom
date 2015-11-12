<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType;

/**
 * Игровая локация
 * @ORM\Table(name="rooms", uniqueConstraints={@ORM\UniqueConstraint(name="room_coordinates", columns={"x", "y", "z"})})
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomRepository")
 */
class Room {

    const DEFAULT_Z = 0;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Название
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @var string
     */
    private $name;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="Rottenwood\KingdomBundle\Entity\Infrastructure\RoomType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     * @var RoomType
     **/
    private $type;

    /**
     * Описание
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;

    /**
     * Положение на карте по оси Х
     * @ORM\Column(name="x", type="integer")
     * @var integer
     */
    private $x;

    /**
     * Положение на карте по оси Y
     * @ORM\Column(name="y", type="integer")
     * @var integer
     */
    private $y;

    /**
     * Положение на карте по оси Z
     * @ORM\Column(name="z", type="integer")
     * @var integer
     */
    private $z;

    /**
     * @param int      $x
     * @param int      $y
     * @param RoomType $type
     * @param int      $z
     */
    public function __construct($x, $y, RoomType $type, $z = self::DEFAULT_Z) {
        $this->x = $x;
        $this->y = $y;
        $this->type = $type;
        $this->z = $z;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name ?: $this->getType()->getName();
    }

    /**
     * @return RoomType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param RoomType $type
     */
    public function setType(RoomType $type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description ?: $this->getType()->getDescription();
    }

    /**
     * @return int
     */
    public function getX() {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY() {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getZ() {
        return $this->z;
    }

}
