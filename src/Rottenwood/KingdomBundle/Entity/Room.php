<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Игровая локация
 * @ORM\Table(name="rooms")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\RoomRepository")
 */
class Room {

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Название
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="RoomType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     * @var RoomType
     **/
    private $type;

    /**
     * Описание
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * Положение на карте по оси Х
     * @var integer
     * @ORM\Column(name="x", type="integer")
     */
    private $x;

    /**
     * Положение на карте по оси Y
     * @var integer
     * @ORM\Column(name="y", type="integer")
     */
    private $y;

    /**
     * Положение на карте по оси Z
     * @var integer
     * @ORM\Column(name="z", type="integer")
     */
    private $z;

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
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
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
