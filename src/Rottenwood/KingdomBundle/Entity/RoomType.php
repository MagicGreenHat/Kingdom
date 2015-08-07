<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип локации
 * @ORM\Table(name="room_types", uniqueConstraints={@ORM\UniqueConstraint(name="index_unique_name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Rottenwood\KingdomBundle\Entity\RoomTypeRepository")
 */
class RoomType {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Название
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @var string
     */
    private $name;

    /**
     * Описание
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;

    /**
     * Имя изображения на карте
     * @ORM\Column(name="picture", type="string", length=255)
     * @var string
     */
    private $picture;

    /**
     * @param string $name
     * @param string $description
     * @param string $picture
     */
    public function __construct($name, $description, $picture) {
        $this->name = $name;
        $this->description = $description;
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
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPicture() {
        return $this->picture;
    }
}
