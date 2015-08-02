<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип локации
 * @ORM\Table(name="room_types")
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\RoomTypeRepository")
 */
class RoomType {

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
     * Имя изображения на карте
     * @var string
     * @ORM\Column(name="picture", type="string", length=255)
     */
    private $picture;

    /**
     * @param string $name
     * @param string $picture
     */
    public function __construct($name, $picture) {
        $this->name = $name;
        $this->picture = $picture;
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
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPicture() {
        return $this->picture;
    }
}
